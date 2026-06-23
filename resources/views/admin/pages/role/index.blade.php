@extends('admin.master')

@section('title', 'Role-Management')
@section('quickAccessicon', 'ri-user-settings-line')

@section('content')
    <main id="role">
        <div class="row">
            <div class="col-lg-4">
                <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-admin-line"></i> Create Roles
                    </h5>
                    <form @submit.prevent="createRole">
                        <div class="row mb-3">
                            <label class="col-3 col-form-label">Name</label>
                            <div class="col-9">
                                <input type="text" class="form-control form-control-sm" v-model="roleName"
                                    :class="{ 'is-invalid': errors.name }">
                                <div v-if="errors.name" class="text-danger small">@{{ errors.name[0] }}</div>
                            </div>
                        </div>
                        <div class="row" style="align-items: end">
                            <div class="text-end">
                                <button type="submit" class="btn btn-success mt-2">
                                    <i class="ri-save-line"></i> @{{ btnText }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-list-check"></i> Roles List
                    </h5>
                    <div class="my-2">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th><i class="ri-map-pin-user-line"></i></th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="role in roles" :key="role.id">
                                    <td>@{{ role.name }}</td>
                                    <td>@{{ role.users_count }} Users</td>
                                    <td>
                                        <a v-if="role.name !== 'User'" href="javascript: void(0);" @click="editRow(role)"
                                            class="text-reset fs-16 px-1">
                                            <i class="ri-edit-line"></i></a>
                                        <a v-if="role.name !== 'User'" href="javascript: void(0);"
                                            @click="deleteRow(role.id)" class="text-reset fs-16 px-1"><i
                                                class="ri-delete-bin-2-line"></i></a>
                                        <div v-if="role.name === 'User'" class="text-primary small">Default Role</div>
                                    </td>
                                </tr>
                                <tr v-if="roles.length === 0">
                                    <td colspan="3">No Data Found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!--
                            1 .this code is for adding new persmissions
                            2. if you add any new permission please make sure you are also adding them on permission seeder
                            3. permission first word before _ is the category or group name
                        -->

                {{-- <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-user-settings-line"></i> Create Permissions
                    </h5>
                    <form @submit.prevent="createPermission">
                        <div class="row mb-3">
                            <label class="col-3 col-form-label">Name</label>
                            <div class="col-9">
                                <input type="text" class="form-control form-control-sm" v-model="permissionName"
                                    :class="{ 'is-invalid': errors.name }">
                                <div v-if="errors.name" class="text-danger small">@{{ errors.name[0] }}</div>
                            </div>
                        </div>
                        <div class="row" style="align-items: end">
                            <div class="text-end">
                                <button type="submit" class="btn btn-success mt-2">
                                    <i class="ri-save-line"></i> @{{ btnText }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div> --}}
            </div>
            <div class="col-lg-8">
                <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-group-line"></i> Roles Has Permission
                    </h5>
                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Select Role</label>
                        <div class="col-9">
                            <select name="" id="role-select" class="form-control" v-model="selectedRole"
                                @change="loadPermissionsForRole">
                                <option value="" disabled selected>-- Select Role --</option> <!-- Default option -->
                                <option v-for="role in roles" :key="role.id" :value="role.id">
                                    @{{ role.name }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4" v-for="(permGroup, groupName) in groupedPermissions" :key="groupName">
                            <input type="checkbox" class="form-check-input" :id="'group-' + groupName"
                                @change="toggleGroupPermissions(groupName, $event)"><label
                                class="form-label"><i>@{{ groupName }}</i></label>
                            <div v-for="perm in permGroup" :key="perm.id"
                                class="form-check form-checkbox-info mb-2">
                                <input type="checkbox" class="form-check-input" :id="'permission-' + perm.id"
                                    :value="perm.id" v-model="assignedPermissions">
                                <label class="form-check-label"
                                    :for="'permission-' + perm.id">@{{ perm.name }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="align-items: end">
                        <div class="text-end">
                            <button type="submit" class="btn btn-info mt-2" @click="assignPermissions">
                                <i class="ri-save-line"></i> Assign Permissions
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>


@endsection

@push('script')
    <script src="{{ asset('vue/vue.js') }}"></script>
    <script src="{{ asset('vue/axios.min.js') }}"></script>
    <script src="{{ asset('vue/moment.js') }}"></script>
    <script src="{{ asset('vue/vuejs-datatable.js') }}"></script>

    <script>
        new Vue({
            el: '#role',
            data() {
                return {
                    roleId: 0,
                    roleName: '',
                    roles: [],
                    errors: {},
                    btnText: 'Create',

                    permissions: [],
                    selectedRole: "",
                    assignedPermissions: [],
                    groupedPermissions: {}
                };
            },

            created() {
                this.getRoles();
                this.getPermissions();
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

                // Create a new role
                async createRole() {
                    try {
                        let url = '';
                        let message = '';
                        if (this.roleId != 0) {
                            url = '/dashboard/update/roles';
                            message = "Role Updated";
                        } else {
                            url = '/dashboard/create/roles';
                            message = "Role Added";
                        }
                        let response = await axios.post(url, {
                            id: this.roleId,
                            name: this.roleName
                        });
                        this.getRoles();
                        this.roleName = '';
                        this.errors = {};
                        Toast.fire({
                            icon: 'success',
                            title: message,
                        });
                    } catch (error) {
                        if (error.response && error.response.data.errors) {
                            this.errors = error.response.data.errors; // Show validation errors
                        }
                    }
                },

                // Edit a role (to be implemented)
                async editRow(row) {
                    this.roleId = row.id;
                    this.roleName = row.name;
                    this.btnText = 'Update';
                },

                // Delete a role
                async deleteRow(roleId) {
                    try {
                        const {
                            value: password
                        } = await Swal.fire({
                            icon: 'info',
                            title: "Are you sure you want to delete this role?",
                            input: "password",
                            inputLabel: "If you delete this role all the user have this role will be default user",
                            inputPlaceholder: "Enter your password",
                            inputAttributes: {
                                maxlength: "100",
                                autocapitalize: "off",
                                autocorrect: "off"
                            },
                            confirmButtonText: "Yes",
                            showCancelButton: true,
                            cancelButtonText: "No"
                        });

                        if (password) {
                            let response = await axios.post('/dashboard/destroy/roles', {
                                password: password,
                                id: roleId
                            });

                            // Axios does not throw an error for 2xx responses
                            if (response.status) {
                                Toast.fire({
                                    icon: 'success',
                                    title: response.data.message,
                                });
                                this.getRoles();
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: response.data.message,
                                });
                            }
                        }
                    } catch (error) {
                        console.error('Error deleting role:', error);

                        // If the error is an Axios response error, handle it
                        if (error.response) {
                            Toast.fire({
                                icon: 'error',
                                title: error.response.data.message || 'Something went wrong',
                            });
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: 'An unexpected error occurred',
                            });
                        }
                    }
                },

                // Permissions

                async createPermission() {
                    try {
                        let url = '/dashboard/create/permission';
                        message = "Permission Added";
                        let response = await axios.post(url, {
                            name: this.permissionName
                        });
                        this.permissionName = '';
                        this.errors = {};
                        Toast.fire({
                            icon: 'success',
                            title: message,
                        });
                        this.getPermissions();
                    } catch (error) {
                        if (error.response && error.response.data.errors) {
                            this.errors = error.response.data.errors; // Show validation errors
                        }
                    }
                },

                async getPermissions() {
                    try {
                        const response = await axios.get('/dashboard/get/permissions');
                        this.permissions = response.data;

                        this.groupPermissionsByCategory();
                    } catch (error) {
                        console.error('Error fetching permissions:', error);
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

                async loadPermissionsForRole() {
                    if (!this.selectedRole) return;

                    try {
                        const response = await axios.get(`/dashboard/roles/${this.selectedRole}/permissions`);
                        this.assignedPermissions = response.data.permissions.map(p => p.id);
                    } catch (error) {
                        console.error('Error loading permissions for role:', error);
                    }
                },

                async assignPermissions() {
                    if (!this.selectedRole) return;

                    try {
                        await axios.post(`/dashboard/roles/${this.selectedRole}/assign-permissions`, {
                            permissions: this.assignedPermissions
                        });

                        Toast.fire({
                            icon: 'success',
                            title: 'Permissions successfully updated'
                        });
                    } catch (error) {
                        console.error('Error assigning permissions:', error);
                    }
                },

                toggleGroupPermissions(groupName, event) {
                    const isChecked = event.target.checked;
                    const groupPermissions = this.groupedPermissions[groupName].map(perm => perm.id);

                    if (isChecked) {
                        // Add all permissions from the group if not already selected
                        this.assignedPermissions = [...new Set([...this.assignedPermissions, ...groupPermissions])];
                    } else {
                        // Remove all permissions from the group
                        this.assignedPermissions = this.assignedPermissions.filter(permId => !groupPermissions
                            .includes(permId));
                    }
                }

            }

        });
    </script>
@endpush
