<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showCreateForm = false;
    public $editId = null;

    // Form properties
    public string $username = '';
    public string $fullname = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'user';
    public string $description = '';
    public string $cap = '';

    protected function rules()
    {
        return [
            'username'    => 'required|unique:mlite_users,username,' . $this->editId,
            'fullname'    => 'required',
            'email'       => 'required|email',
            'password'    => $this->editId ? 'nullable|min:8' : 'required|min:8',
            'role'        => 'required|in:admin,user',
            'description' => 'nullable|string|max:500',
            'cap'         => 'nullable|string|max:255',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
        $this->resetValidation();
        if (!$this->showCreateForm) {
            $this->reset(['editId', 'username', 'fullname', 'email', 'password', 'role', 'description', 'cap']);
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->editId = $user->id;
        $this->username = $user->username;
        $this->fullname = $user->fullname;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->description = $user->description ?? '';
        $this->cap = $user->cap ?? '';
        $this->password = '';
        
        $this->showCreateForm = true;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        if ($this->editId) {
            $user = User::findOrFail($this->editId);
            $data = [
                'username'    => $this->username,
                'fullname'    => $this->fullname,
                'email'       => $this->email,
                'role'        => $this->role,
                'description' => $this->description,
                'cap'         => $this->cap,
            ];
            
            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }
            
            $user->update($data);
            $this->dispatch('swal', [
                'title' => 'Success!',
                'text'  => 'User successfully updated.',
                'icon'  => 'success',
            ]);
        } else {
            User::create([
                'username'    => $this->username,
                'fullname'    => $this->fullname,
                'email'       => $this->email,
                'password'    => Hash::make($this->password),
                'role'        => $this->role,
                'description' => $this->description,
                'avatar'      => '',
                'cap'         => $this->cap,
                'access'      => 'all',
            ]);
            $this->dispatch('swal', [
                'title' => 'Success!',
                'text'  => 'User successfully created.',
                'icon'  => 'success',
            ]);
        }

        $this->toggleCreateForm();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('fullname', 'like', '%' . $this->search . '%')
                      ->orWhere('username', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.user.index', [
            'users' => $users,
        ]);
    }
}
