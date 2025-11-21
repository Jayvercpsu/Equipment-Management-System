<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;
use App\Models\AccessoryModel;

class EquipmentController extends BaseController
{
    protected $equipmentModel;
    protected $accessoryModel;
    
    public function __construct()
    {
        $this->equipmentModel = new EquipmentModel();
        $this->accessoryModel = new AccessoryModel();
        helper(['form', 'url']);
    }
    
    public function index()
    {
        $perPage = 10;
        $search = $this->request->getGet('search');
        
        if ($search) {
            $equipment = $this->equipmentModel->searchEquipment($search);
            $pager = null;
        } else {
            $equipment = $this->equipmentModel->paginate($perPage);
            $pager = $this->equipmentModel->pager;
        }
        
        $data = [
            'title' => 'Equipment Management',
            'equipment' => $equipment,
            'pager' => $pager,
            'search' => $search
        ];
        
        return view('admin/equipment/index', $data);
    }
    
    public function create()
    {
        $data = [
            'title' => 'Add Equipment',
            'equipment' => null,
            'next_item_id' => $this->equipmentModel->getNextItemId()
        ];
        
        return view('admin/equipment/form', $data);
    }
    
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'category' => 'required',
            'stock_count' => 'required|integer',
            'minimum_stock' => 'required|integer',
            'status' => 'required|in_list[available,borrowed,unusable,reserved]'
        ];
        
        if ($this->request->getFile('image')->isValid()) {
            $rules['image'] = 'uploaded[image]|max_size[image,2048]|is_image[image]';
        }
        
        $validation->setRules($rules);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $imagePath = null;
        $thumbnailPath = null;
        
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid()) {
            $newName = $imageFile->getRandomName();
            $imageFile->move(FCPATH . 'assets/uploads/equipment/images', $newName);
            $imagePath = 'assets/uploads/equipment/images/' . $newName;
            
            $image = \Config\Services::image()
                ->withFile(FCPATH . $imagePath)
                ->resize(300, 200, true, 'height')
                ->save(FCPATH . 'assets/uploads/equipment/thumbnails/' . $newName);
            
            $thumbnailPath = 'assets/uploads/equipment/thumbnails/' . $newName;
        }
        
        $data = [
            'item_id' => $this->request->getPost('item_id'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'category' => $this->request->getPost('category'),
            'brand' => $this->request->getPost('brand'),
            'model' => $this->request->getPost('model'),
            'serial_number' => $this->request->getPost('serial_number'),
            'stock_count' => $this->request->getPost('stock_count'),
            'minimum_stock' => $this->request->getPost('minimum_stock'),
            'status' => $this->request->getPost('status'),
            'image_path' => $imagePath,
            'thumbnail_path' => $thumbnailPath,
            'created_by' => session()->get('user_id')
        ];
        
        if ($this->equipmentModel->insert($data)) {
            $equipmentId = $this->equipmentModel->getInsertID();
            
            $accessories = $this->request->getPost('accessories');
            if ($accessories && is_array($accessories)) {
                foreach ($accessories as $accessory) {
                    if (!empty($accessory['name'])) {
                        $this->accessoryModel->insert([
                            'equipment_id' => $equipmentId,
                            'name' => $accessory['name'],
                            'quantity' => $accessory['quantity'] ?? 1,
                            'required_when_borrowed' => isset($accessory['required']) ? 1 : 0
                        ]);
                    }
                }
            }
            
            return redirect()->to('/admin/equipment')->with('success', 'Equipment added successfully');
        }
        
        return redirect()->back()->withInput()->with('error', 'Failed to add equipment');
    }
    
    public function edit($id)
    {
        $equipment = $this->equipmentModel->find($id);
        
        if (!$equipment) {
            return redirect()->to('/admin/equipment')->with('error', 'Equipment not found');
        }
        
        $data = [
            'title' => 'Edit Equipment',
            'equipment' => $equipment,
            'accessories' => $this->accessoryModel->getEquipmentAccessories($id)
        ];
        
        return view('admin/equipment/form', $data);
    }
    
    public function update($id)
    {
        $equipment = $this->equipmentModel->find($id);
        
        if (!$equipment) {
            return redirect()->to('/admin/equipment')->with('error', 'Equipment not found');
        }
        
        $validation = \Config\Services::validation();
        
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'category' => 'required',
            'stock_count' => 'required|integer',
            'minimum_stock' => 'required|integer',
            'status' => 'required|in_list[available,borrowed,unusable,reserved]'
        ];
        
        if ($this->request->getFile('image')->isValid()) {
            $rules['image'] = 'uploaded[image]|max_size[image,2048]|is_image[image]';
        }
        
        $validation->setRules($rules);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $imagePath = $equipment['image_path'];
        $thumbnailPath = $equipment['thumbnail_path'];
        
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid()) {
            if ($imagePath && file_exists(FCPATH . $imagePath)) {
                unlink(FCPATH . $imagePath);
            }
            if ($thumbnailPath && file_exists(FCPATH . $thumbnailPath)) {
                unlink(FCPATH . $thumbnailPath);
            }
            
            $newName = $imageFile->getRandomName();
            $imageFile->move(FCPATH . 'assets/uploads/equipment/images', $newName);
            $imagePath = 'assets/uploads/equipment/images/' . $newName;
            
            $image = \Config\Services::image()
                ->withFile(FCPATH . $imagePath)
                ->resize(300, 200, true, 'height')
                ->save(FCPATH . 'assets/uploads/equipment/thumbnails/' . $newName);
            
            $thumbnailPath = 'assets/uploads/equipment/thumbnails/' . $newName;
        }
        
        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'category' => $this->request->getPost('category'),
            'brand' => $this->request->getPost('brand'),
            'model' => $this->request->getPost('model'),
            'serial_number' => $this->request->getPost('serial_number'),
            'stock_count' => $this->request->getPost('stock_count'),
            'minimum_stock' => $this->request->getPost('minimum_stock'),
            'status' => $this->request->getPost('status'),
            'image_path' => $imagePath,
            'thumbnail_path' => $thumbnailPath
        ];
        
        if ($this->equipmentModel->update($id, $data)) {
            return redirect()->to('/admin/equipment')->with('success', 'Equipment updated successfully');
        }
        
        return redirect()->back()->withInput()->with('error', 'Failed to update equipment');
    }
    
    public function delete($id)
    {
        $equipment = $this->equipmentModel->find($id);
        
        if (!$equipment) {
            return redirect()->to('/admin/equipment')->with('error', 'Equipment not found');
        }
        
        if ($this->equipmentModel->delete($id)) {
            return redirect()->to('/admin/equipment')->with('success', 'Equipment deleted successfully');
        }
        
        return redirect()->to('/admin/equipment')->with('error', 'Failed to delete equipment');
    }
    
    public function view($id)
    {
        $equipment = $this->equipmentModel->find($id);
        
        if (!$equipment) {
            return redirect()->to('/admin/equipment')->with('error', 'Equipment not found');
        }
        
        $data = [
            'title' => 'Equipment Details',
            'equipment' => $equipment,
            'accessories' => $this->accessoryModel->getEquipmentAccessories($id)
        ];
        
        return view('admin/equipment/view', $data);
    }
}