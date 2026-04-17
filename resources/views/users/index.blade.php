<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/user-style.css') }}">

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Users') }}
        </h2>
    </x-slot>

    <div class="user-container">
        <div class="header-section">
            <h3 style="font-size: 1.2rem; font-weight: 600; color: #1e293b;">User Directory</h3>
            <a href="javascript:void(0)" class="btn-add" onclick="openModal()">Add New User</a>
        </div>

        <table class="custom-table">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email Address</th>
                    <th>Joined Date</th> <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <div class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <strong style="color: #334155;">{{ $user->name }}</strong>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        
                        <td>
                            <span style="color: #64748b; font-size: 0.85rem;">
                                {{ $user->created_at->format('d M Y') }} 
                                <small style="display: block; color: #94a3b8;">
                                    {{ $user->created_at->diffForHumans() }}
                                </small>
                            </span>
                        </td>

                        <td>
                            <div class="actions-cell">
                                <a href="javascript:void(0)" class="btn-edit" onclick="openEditModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}')">Edit</a>
                                
                                <button type="button" class="btn-delete" onclick="openDeleteModal('{{ $user->id }}')">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-row">No users registered in the database.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Start Modal Create User --}}
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2>Add New User</h2>
            
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Enter full name" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="email@example.com" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Min. 8 characters" required>
                </div>
                <button type="submit" class="btn-save">Save User</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById("userModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("userModal").style.display = "none";
        }
    </script>
    {{-- End Modal Create User --}}

    {{-- Start Modal Edit User --}}
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeEditModal()">&times;</span>
            <h2>Edit User</h2>
            
            <form id="editForm" method="POST">
                @csrf
                @method('PUT') <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" id="edit_name" placeholder="Enter full name" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" id="edit_email" placeholder="email@example.com" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin diubah">
                </div>
                <button type="submit" class="btn-save">Update User</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById("userModal").style.display = "block";
        }
        function closeModal() {
            document.getElementById("userModal").style.display = "none";
        }

        function openEditModal(id, name, email) {
            const modal = document.getElementById("editUserModal");
            const form = document.getElementById("editForm");

            form.action = '/users/' + id;

            document.getElementById("edit_name").value = name;
            document.getElementById("edit_email").value = email;
            
            modal.style.display = "block";
        }

        function closeEditModal() {
            document.getElementById("editUserModal").style.display = "none";
        }
    </script>
    {{-- End Modal Edit User --}}

    {{-- Start Modal Delete User --}}
    <div id="deleteUserModal" class="modal">
        <div class="modal-content" style="width: 350px; text-align: center;">
            <span class="close-modal" onclick="closeDeleteModal()">&times;</span>
            <h2 style="color: #dc2626;">Are you sure?</h2>
            <p style="color: #64748b; margin-bottom: 25px;">This action cannot be undone. All data for this user will be removed.</p>
            
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button type="button" onclick="closeDeleteModal()" style="background: #e2e8f0; color: #475569; padding: 10px 20px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600;">
                        Cancel
                    </button>
                    <button type="submit" class="btn-delete" style="padding: 10px 20px; background-color: #dc2626; color: white;">
                        Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDeleteModal(id) {
            const modal = document.getElementById("deleteUserModal");
            const form = document.getElementById("deleteForm");
            
            form.action = '/users/' + id;
            
            modal.style.display = "block";
        }

        function closeDeleteModal() {
            document.getElementById("deleteUserModal").style.display = "none";
        }
    </script>
    {{-- End Modal Delete User --}}

    <script>
        window.onclick = function(event) {
            let addModal = document.getElementById("userModal");
            let editModal = document.getElementById("editUserModal");
            let deleteModal = document.getElementById("deleteUserModal");
            
            if (event.target == addModal) closeModal();
            if (event.target == editModal) closeEditModal();
            if (event.target == deleteModal) closeDeleteModal();
        }
    </script>
    
</x-app-layout>