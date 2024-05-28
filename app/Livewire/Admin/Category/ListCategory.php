<?php

namespace App\Livewire\Admin\Category;

use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

#[Layout('components.admin.main')]
class ListCategory extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $id;
    public $name;
    public $mainCategories;
    public $main_category;

    public function mount()
    {
        $this->mainCategories = Category::whereNull('parent_id')->get();
    }

    public function render()
    {
        Session::flash('title', 'Danh mục');

        return view('livewire.admin.category.list-category', [
            'categories' => Category::whereNotNull('parent_id')->paginate(6),
            'directory' => 'Danh sách',
        ]);
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:categories,name',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên danh mục phụ không được để trống.',
            'name.unique' => 'Tên danh mục phụ đã tồn tại.',
        ];
    }

    public function openEditModal($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $this->id = $category->id;
        $this->name = $category->name;
        $this->main_category = $category->parent_id;

        $this->dispatch('openModal');
    }

    public function updateCategory()
    {
        $this->validate();

        $category = Category::findOrFail($this->id);

        $data = [
            'name' => $this->name,
            'parent_id' => $this->main_category,
        ];

        try {
            $category->update($data);

            $this->dispatch('closeModal');

            Session::flash('success', 'Cập nhập thành công!');
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
        }
    }

    public function deleteCategory($categoryId)
    {
        $category = Category::find($categoryId);

        if ($category) {
            try {
                $category->delete();

                Session::flash('success', 'Xoá danh mục thành công.');
            } catch (Exception $e) {
                Session::flash('error', $e->getMessage());
            }
        } else {
            Session::flash('error', 'Không tìm thấy danh mục.');
        }
    }
}
