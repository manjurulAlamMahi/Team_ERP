@extends('admin.master')

@section('title', 'Dashboard')
@section('quickAccessicon', 'ri-chat-1-fill')

@section('content')
    <div id="inbox">
        <div class="row">
            <!-- start chat users-->
            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <img src="{{ asset(Auth::user()->avatar) }}" class="me-2 rounded-circle" height="42"
                                alt="Brandon Smith">
                            <div class="w-100">
                                <h5 class="mb-0">
                                    <a href="pages-profile.html" class="text-reset lh-base">{{ Auth::user()->name }}</a>
                                </h5>
                                <p class="mb-0 text-muted">
                                    <small class="ri-checkbox-blank-circle-fill text-success"></small> Online
                                </p>
                            </div>
                            <a href="{{ route('profile.index') }}" class="text-reset fs-20">
                                <i class="ri-settings-5-line"></i>
                            </a>
                        </div>

                        <!-- start search box -->
                        <div class="app-search">
                            <form action="#">
                                <div class="mb-1 w-100 position-relative">
                                    <input type="search" class="form-control" v-model="searchQuery"
                                        placeholder="People, groups & messages...">
                                    <span class="ri-search-line search-icon"></span>
                                </div>
                            </form>
                        </div>
                        <!-- end search box -->
                    </div> <!-- end card-body-->

                    <div class="card-body p-0 pb-3">
                        <!-- users -->
                        <div class="row">
                            <div class="col">
                                <!-- Contacts -->
                                <h6 v-if="filteredContacts.length > 0" class="fs-13 text-muted text-uppercase mx-3">Contacts
                                </h6>
                                <div>
                                    <a v-for="user in filteredContacts" :key="user.id" href="javascript: void(0);"
                                        class="text-body">
                                        <div class="d-flex align-items-start py-2 px-3" @click="startConversation(user.id)">
                                            <img :src="getAvatarUrl(user.avatar)" v-if="user.avatar" :alt="user.name"
                                                class="me-2 rounded-circle" height="42" alt="User Avatar" />
                                            <div class="w-100">
                                                <h5 class="my-0">
                                                    <span
                                                        class="float-end text-muted fw-normal fs-12">@{{ user.last_message_time }}</span>
                                                    @{{ user.name }}
                                                </h5>
                                                <p class="mt-1 mb-0 text-muted">
                                                    <span v-if="user.unread_count > 0" class="w-25 float-end text-end">
                                                        <span class="badge bg-danger-subtle text-danger">unread
                                                            @{{ user.unread_count }}</span>
                                                    </span>
                                                    <span class="w-75">@{{ user.last_message || 'No messages yet' }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- Start New Conversation -->
                                <h6 v-if="filteredUsers.length > 0" class="fs-13 text-muted text-uppercase mx-3 mt-3">Start
                                    Conversation With</h6>
                                <div>
                                    <a v-for="user in filteredUsers" :key="user.id" href="javascript: void(0);"
                                        class="text-body">
                                        <div class="d-flex align-items-start py-2 px-3" @click="startConversation(user.id)">
                                            <img :src="getAvatarUrl(user.avatar)" v-if="user.avatar" :alt="user.name"
                                                class="me-2 rounded-circle" height="42" alt="User Avatar" />
                                            <div class="w-100">
                                                <h5 class="my-0">@{{ user.name }}</h5>
                                                <p class="mt-1 mb-0 text-muted"><span class="w-75">No messages yet</span>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end users -->
                </div>
            </div> <!-- end card-->

            <!-- chat area -->
            <div class="col-xl-9 col-lg-8">
                <template v-if="conversationWithID != null">
                    <div class="card">
                        <div class="card-body py-2 px-3 border-bottom border-light">
                            <div class="row justify-content-between py-1">
                                <div class="col-sm-7">
                                    <div class="d-flex align-items-start">
                                        <img v-if="conversationWith.avatar != null"
                                            :src="getAvatarUrl(conversationWith.avatar)" class="me-2 rounded-circle"
                                            height="36" alt="Brandon Smith">
                                        <div>
                                            <h5 class="my-0 font-15">
                                                <a v-if="conversationWith.name"
                                                    :href="`/dashboard/user-profile/${conversationWith.username}`"
                                                    class="text-reset">@{{ conversationWith.name }}</a>
                                            </h5>
                                            <p class="mt-1 mb-0 text-muted fs-12">
                                                <small v-if="conversationWith.is_online"
                                                    class="ri-checkbox-blank-circle-fill text-success"></small>
                                                <small v-else class="ri-checkbox-blank-circle-fill text-danger"></small>
                                                @{{ conversationWith.is_online ? 'Online' : 'Offline' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div id="tooltips-container">
                                        <a href="javascript: void(0);" @click="refreshMessages" class="text-reset fs-20 p-1 d-inline-block">
                                            <i class="ri-refresh-line"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-0">

                            <ul class="conversation-list p-3" data-simplebar style="max-height: 520px;">
                                <li v-for="(message, index) in conversationMessages" :key="index" class="clearfix"
                                    :class="{ 'odd': message.sender_id !== conversationWith.id }">
                                    <div class="chat-avatar">
                                        <!-- Avatar dynamically based on the message sender -->
                                        <img :src="getAvatarUrl(message.sender.avatar)" class="rounded"
                                            :alt="conversationWith.name" />
                                        <i>@{{ formatTime(message.created_at) }}</i>
                                    </div>
                                    <div class="conversation-text">
                                        <div class="ctext-wrap">
                                            <i>@{{ message.sender.name }}</i>
                                            <p>@{{ message.message }}</p>
                                        </div>
                                    </div>
                                    <div class="conversation-actions dropdown">
                                        <button class="btn btn-sm btn-link fs-18" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ri-more-2-fill"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-animated"
                                            :class="{ 'dropdown-menu-end': message.sender_id == conversationWith.id }">
                                            <a class="dropdown-item" href="javascript:void(0);"
                                                @click="copyMessage(message.message)">Copy Message</a>
                                            <a v-if="message.sender_id !== conversationWith.id" class="dropdown-item"
                                                href="javascript:void(0);" @click="deleteMessage(message.id)">Delete</a>
                                        </div>
                                    </div>
                                </li>
                            </ul>


                            <div class="row">
                                <div class="col">
                                    <div class="bg-light p-3 rounded">
                                        <form class="needs-validation" id="chat-form" @submit.prevent="sendMessage">
                                            <div class="row">
                                                <div class="col mb-2 mb-sm-0">
                                                    <input type="text" class="form-control border-0"
                                                        placeholder="Enter your text" v-model="message" required="" />
                                                    <div class="invalid-feedback">
                                                        Please enter your messsage
                                                    </div>
                                                </div>
                                                <div class="col-sm-auto">
                                                    <div class="btn-group">
                                                        <button type="submit" class="btn btn-success chat-send w-100"><i
                                                                class="ri-send-plane-2-line"></i></button>
                                                    </div>
                                                </div>
                                                <!-- end col -->
                                            </div>
                                            <!-- end row-->
                                        </form>
                                    </div>
                                </div>
                                <!-- end col-->
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end card-body -->

                    </div>
                </template>
                <template v-else>
                    <div class="card">
                        <div class="card-body py-2 px-3 border-bottom border-light">
                            <div class="text-center my-3">
                                <h2>Start Conversation</h2>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <!-- end chat area-->
        </div>
        <!-- end chat users-->
    </div> <!-- end row-->
@endsection

@push('script')
    <script src="{{ asset('vue/vue.js') }}"></script>
    <script src="{{ asset('vue/axios.min.js') }}"></script>
    <script src="{{ asset('vue/moment.js') }}"></script>
    <script src="{{ asset('vue/vuejs-datatable.js') }}"></script>

    <script>
        new Vue({
            el: '#inbox',
            data() {
                return {
                    searchQuery: "",
                    users: [],
                    contact_users: [],
                    conversationWithID: {{ request()->query('user') ? (int) request()->query('user') : 'null' }},
                    conversationWith: null,
                    conversationMessages: [],
                    message: null,
                };
            },

            computed: {
                filteredContacts() {
                    return this.contact_users.filter(user =>
                        user.name.toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                },
                filteredUsers() {
                    return this.users.filter(user =>
                        user.name.toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                }
            },

            created() {
                this.getUsers();
                if (this.conversationWithID != null) {
                    this.getConversationUser();
                }
            },

            methods: {
                getUsers() {
                    axios.get('/dashboard/get/conversation/users')
                        .then(response => {
                            this.contact_users = response.data.data.contact_users;
                            this.users = response.data.data.users;
                            console.log(this.users);

                        })
                        .catch(error => {
                            console.error("Error fetching users:", error);
                        });
                },

                getConversationUser() {
                    if (!this.conversationWithID) return; // Prevent unnecessary API calls

                    axios.post('/dashboard/chat/get', {
                            receiver_id: this.conversationWithID // Send receiver_id in the body
                        })
                        .then(response => {
                            this.conversationWith = response.data.data.receiver; // Store user info
                            this.conversationMessages = response.data.data.messages; // Store messages
                            console.log(this.conversationWith);

                        })
                        .catch(error => {
                            console.error("Error fetching conversation user:", error);
                        });
                },


                startConversation(id) {
                    this.conversationWithID = id;
                    this.getConversationUser();
                },

                getAvatarUrl(avatar) {
                    return avatar ? `${window.location.origin}/${avatar}` :
                        '/default-avatar.png'; // Use a default avatar if needed
                },

                formatTime(date) {
                    const time = new Date(date);
                    return time.getHours() + ':' + (time.getMinutes() < 10 ? '0' + time.getMinutes() : time
                        .getMinutes());
                },

                sendMessage() {
                    axios.post('/dashboard/chat/send', {
                            receiver_id: this.conversationWithID,
                            message: this.message,
                        })
                        .then(response => {
                            this.message = '';
                            this.getConversationUser();
                            this.getUsers();
                        });
                },

                deleteMessage(id) {
                    axios.post('/dashboard/chat/delete', {
                            message_id: id,
                        })
                        .then(response => {
                            this.getConversationUser();
                            this.getUsers();
                        });
                },

                copyMessage(message) {
                    navigator.clipboard.writeText(message)
                        .then(() => {
                            console.log('Message copied to clipboard');
                        })
                        .catch(err => {
                            console.error('Failed to copy message:', err);
                        })
                },
                refreshMessages() {
                    this.getConversationUser();
                }
            },
        });
    </script>
@endpush
