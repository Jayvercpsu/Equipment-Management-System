<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;
use App\Models\BorrowModel;
use App\Models\UserModel;

class ReportsController extends BaseController
{
    protected $equipmentModel;
    protected $borrowModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->equipmentModel = new EquipmentModel();
        $this->borrowModel = new BorrowModel();
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }
    
    public function activeEquipment()
    {
        $equipment = $this->equipmentModel->where('status', 'available')
            ->orWhere('status', 'borrowed')
            ->findAll();
        
        $data = [
            'title' => 'Active Equipment Report',
            'equipment' => $equipment
        ];
        
        return view('admin/reports/active_equipment', $data);
    }
    
    public function unusableEquipment()
    {
        $equipment = $this->equipmentModel->getUnusableEquipment();
        
        $data = [
            'title' => 'Unusable Equipment Report',
            'equipment' => $equipment
        ];
        
        return view('admin/reports/unusable_equipment', $data);
    }
    
    public function userHistory()
    {
        $userId = $this->request->getGet('user_id');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        
        $users = $this->userModel->getAllWithRoles();
        
        $borrows = [];
        if ($userId) {
            $query = $this->borrowModel->select('borrows.*, equipment.name as equipment_name, equipment.item_id')
                ->join('equipment', 'equipment.id = borrows.equipment_id')
                ->where('borrows.user_id', $userId);
            
            if ($dateFrom) {
                $query->where('borrows.borrow_date >=', $dateFrom);
            }
            
            if ($dateTo) {
                $query->where('borrows.borrow_date <=', $dateTo);
            }
            
            $borrows = $query->findAll();
        }
        
        $data = [
            'title' => 'User Borrowing History',
            'users' => $users,
            'borrows' => $borrows,
            'selected_user' => $userId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ];
        
        return view('admin/reports/user_history', $data);
    }
    
    public function export($type)
    {
        $format = $this->request->getGet('format') ?? 'csv';
        
        if ($type === 'active-equipment') {
            $data = $this->equipmentModel->where('status', 'available')
                ->orWhere('status', 'borrowed')
                ->findAll();
            $filename = 'active_equipment_' . date('Y-m-d');
            $headers = ['Item ID', 'Name', 'Category', 'Brand', 'Stock Count', 'Status'];
            
            $rows = [];
            foreach ($data as $item) {
                $rows[] = [
                    $item['item_id'],
                    $item['name'],
                    $item['category'],
                    $item['brand'],
                    $item['stock_count'],
                    $item['status']
                ];
            }
        } elseif ($type === 'unusable-equipment') {
            $data = $this->equipmentModel->getUnusableEquipment();
            $filename = 'unusable_equipment_' . date('Y-m-d');
            $headers = ['Item ID', 'Name', 'Category', 'Brand', 'Status'];
            
            $rows = [];
            foreach ($data as $item) {
                $rows[] = [
                    $item['item_id'],
                    $item['name'],
                    $item['category'],
                    $item['brand'],
                    $item['status']
                ];
            }
        } elseif ($type === 'user-history') {
            $userId = $this->request->getGet('user_id');
            
            if (!$userId) {
                return redirect()->back()->with('error', 'Please select a user first');
            }
            
            $data = $this->borrowModel->select('borrows.*, equipment.name as equipment_name, equipment.item_id')
                ->join('equipment', 'equipment.id = borrows.equipment_id')
                ->where('borrows.user_id', $userId)
                ->findAll();
            
            $user = $this->userModel->find($userId);
            $filename = 'user_history_' . ($user['first_name'] ?? 'user') . '_' . date('Y-m-d');
            $headers = ['Borrow Code', 'Equipment', 'Item ID', 'Borrow Date', 'Due Date', 'Status'];
            
            $rows = [];
            foreach ($data as $item) {
                $rows[] = [
                    $item['borrow_code'],
                    $item['equipment_name'],
                    $item['item_id'],
                    date('Y-m-d', strtotime($item['borrow_date'])),
                    date('Y-m-d', strtotime($item['due_date'])),
                    $item['status']
                ];
            }
        } else {
            return redirect()->back()->with('error', 'Invalid report type');
        }
        
        if ($format === 'csv') {
            return $this->exportCSV($filename, $headers, $rows);
        } elseif ($format === 'pdf') {
            return $this->exportPDF($filename, $headers, $rows);
        }
        
        return redirect()->back()->with('error', 'Invalid format');
    }
    
    private function exportCSV($filename, $headers, $rows)
    {
        $response = $this->response;
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        fputcsv($output, $headers);
        
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        
        return $response;
    }
    
    private function exportPDF($filename, $headers, $rows)
    {
        $html = '<html><head><style>
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #1e40af; color: white; }
            </style></head><body>';
        
        $html .= '<h2>' . ucwords(str_replace('-', ' ', $filename)) . '</h2>';
        $html .= '<p>Generated on: ' . date('F d, Y H:i:s') . '</p>';
        
        $html .= '<table>';
        $html .= '<thead><tr>';
        foreach ($headers as $header) {
            $html .= '<th>' . htmlspecialchars($header) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table></body></html>';
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '.pdf"')
            ->setBody($dompdf->output());
    }
}