@extends('admin.master')

@section('title', 'Assign-Role-To')
@section('quickAccessicon', 'ri-user-line')

@push('style')
@endpush

@section('content')
    <div id="assignRole">
        <div class="row">
            <div class="col-lg-3">
                <!-- User information section -->
                <div class="card text-center">
                    <div class="avatar mt-4">
                        <div class="img">
                            <img src="{{ asset($user->avatar) }}" class="rounded-circle avatar-lg img-thumbnail"
                                alt="profile-image">
                        </div>
                        <div class="name">
                            <h4 class="mb-1 mt-1">{{ $user->name }}</h4>
                            <p class="text-muted">{{ $user->role }}</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-start mt-3">
                            <label for="" class="form-label">User Information :</label>
                            <table>
                                <tr>
                                    <td>
                                        <p class="text-muted mb-2"><strong class="me-1">Name</strong></p>
                                    </td>
                                    <td>
                                        <p class="text-muted mb-2"><strong>:</strong></p>
                                    </td>
                                    <td>
                                        <p class="text-muted mb-2"><span class="ms-2">{{ $user->name }}</span></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p class="text-muted mb-2"><strong class="me-1">Mobile</strong></p>
                                    </td>
                                    <td>
                                        <p class="text-muted mb-2"><strong>:</strong></p>
                                    </td>
                                    <td>
                                        <p class="text-muted mb-2"><span class="ms-2">{{ $user->phone ?? 'N/A' }}</span>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p class="text-muted mb-2"><strong class="me-1">Email</strong></p>
                                    </td>
                                    <td>
                                        <p class="text-muted mb-2"><strong>:</strong></p>
                                    </td>
                                    <td>
                                        <p class="text-muted mb-2"><span class="ms-2">{{ $user->email }}</span></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card card-body">
                    <form @submit.prevent="assignRoleToUser">
                        @csrf
                        <h5 class="mb-3 text-uppercase bg-light p-2">
                            <i class="ri-group-line"></i> Role List
                        </h5>
                        <div v-for="role in roles" :key="role.id" class="form-check my-2">
                            <input type="radio" :id="'role-' + role.id" name="role" :value="role.id"
                                v-model="selectedRole" class="form-check-input" @change="loadPermissionsForRole(role.id)">
                            <label class="form-check-label" :for="'role-' + role.id">@{{ role.name }}</label>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-sm btn-primary">Update Role</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-group-line"></i> Permission's That User Have
                    </h5>
                    <div class="row">
                        <div class="col-lg-4" v-for="(permGroup, groupName) in groupedPermissions" :key="groupName">
                            <label class="form-label"><i>@{{ groupName }}</i></label>
                            <div v-for="perm in permGroup" :key="perm.id"
                                class="mb-2">
                                <label class="form-check-label" :for="'permission-' + perm.id">
                                    <span class="ms-2">@{{ perm.name }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>




        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('vue/vue.js') }}"></script>
    <script src="{{ asset('vue/axios.min.js') }}"></script>
    <script src="{{ asset('vue/moment.js') }}"></script>
    <script src="{{ asset('vue/vuejs-datatable.js') }}"></script>
    <script>
        new Vue({
            el: '#assignRole',
            data() {
                return {
                    roles: [],
                    permissions: [],
                    user_permissions: @json($user->permissions()->pluck('id')),

                    assignedPermissions: [],
                    groupedPermissions: {},
                    selectedRole: "{{ $user->roles()->first()->id }}",
                };
            },

            created() {
                this.getRoles();
                this.loadPermissionsForRole(this.selectedRole);
            },

            watch: {

            },

            methods: {
                // Fetch roles from the API
                async getRoles() {
                    try {
                        const response = await axios.get('/dashboard/get/roles');
                        this.roles = response.data;
                    } catch (error) {
                        console.error('Error fetching roles:', error);
                    }
                },

                // Submit role update
                async assignRoleToUser() {
                    try {
                        const {
                            value: password
                        } = await Swal.fire({
                            icon: 'warning',
                            title: "Confirm Your Action",
                            input: "password",
                            inputLabel: "Please enter your password to confirm the role change",
                            inputPlaceholder: "Your password",
                            inputAttributes: {
                                maxlength: "100",
                                autocapitalize: "off",
                                autocorrect: "off"
                            },
                            confirmButtonText: "Confirm",
                            showCancelButton: true,
                            cancelButtonText: "Cancel"
                        });

                        if (password) {
                            let response = await axios.post('/dashboard/assign-role-to-user', {
                                userId: {{ $user->id }},
                                role: this.selectedRole,
                                password: password,
                            });
                            if (response.data.status == 'success') {
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Role have been assigned to user',
                                });
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: 'Incorrect Password!',
                                });
                            }
                        }


                    } catch (error) {
                        console.error('Error updating role and permissions:', error);
                    }
                },

                async loadPermissionsForRole(roleId) {
                    if (!roleId) return;

                    try {
                        let response = await axios.get(`/dashboard/roles/${roleId}/permissions`);
                        this.permissions = response.data.permissions;
                        this.assignedPermissions = [...this.user_permissions];

                        this.groupPermissionsByCategory();
                    } catch (error) {
                        console.error('Error loading permissions for role:', error);
                    }
                },

                groupPermissionsByCategory() {
                    this.groupedPermissions = this.permissions.reduce((groups, permission) => {
                        const category = permission.name.split('_')[
                            0]; // Assuming 'dashboard_', 'user_' etc.
                        if (!groups[category]) {
                            groups[category] = [];
                        }
                        groups[category].push(permission);
                        return groups;
                    }, {});
                },

                // async updatePermission() {

                //     try {
                //         const {
                //             value: password
                //         } = await Swal.fire({
                //             icon: 'warning',
                //             title: "Confirm Your Action",
                //             input: "password",
                //             inputLabel: "Please enter your password to confirm the role change",
                //             inputPlaceholder: "Your password",
                //             inputAttributes: {
                //                 maxlength: "100",
                //                 autocapitalize: "off",
                //                 autocorrect: "off"
                //             },
                //             confirmButtonText: "Confirm",
                //             showCancelButton: true,
                //             cancelButtonText: "Cancel"
                //         });

                //         if (password) {
                //             let roleId = this.selectedRole; // Get role ID from route or state

                //             let response = await axios.post('/dashboard/roles/update-permissions', {
                //                 user_id: {{ $user->id }},
                //                 role_id: roleId,
                //                 password: password,
                //                 assigned_permissions: this.assignedPermissions
                //             });
                //             if (response.data.status == 'success') {
                //                 Toast.fire({
                //                     icon: 'success',
                //                     title: 'Permissions updated successfully!',
                //                 });
                //                 setTimeout(function() {
                //                     location.reload();
                //                 }, 2000);
                //             } else {
                //                 Toast.fire({
                //                     icon: 'error',
                //                     title: 'Incorrect Password!',
                //                 });
                //             }

                //         }

                //     } catch (error) {
                //         console.error('Error updating permissions:', error);
                //     }
                // }

            }
        });
    </script>
@endpush
