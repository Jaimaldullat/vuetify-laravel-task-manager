<template>
    <div>
        <v-navigation-drawer
            v-model="primaryNavigationDrawer"
            app
        >
            <v-list dense>
                <v-list-item link
                             v-for="item in menuItems"
                             :key="item.title"
                             :to="item.link">
                    <v-list-item-action>
                        <v-icon>{{item.icon}}</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{item.title}}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar
            app
            color="indigo"
            dark
        >
            <v-app-bar-nav-icon @click.stop="primaryNavigationDrawer = !primaryNavigationDrawer"/>
            <v-toolbar-title>Task Manager</v-toolbar-title>
        </v-app-bar>

        <v-main>
            <v-simple-table>
                <template v-slot:default>
                    <thead>
                    <tr>
                        <th class="text-left">Title</th>
                        <th class="text-left">Description</th>
                        <th class="text-left">Start Date</th>
                        <th class="text-left">Due Date</th>
                        <th class="text-left">Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="task in tasks" :key="task.id">
                        <td class="text-no-wrap">{{ task.title }}</td>
                        <td class="text-wrap">{{ task.description }}</td>
                        <td class="text-no-wrap">{{ task.start_date }}</td>
                        <td class="text-no-wrap">{{ task.due_date }}</td>
                        <td class="text-no-wrap">{{ task.status }}</td>
                        <td class="text-no-wrap">
                            <v-btn color="primary" @click.stop="editTask(task)">Edit</v-btn>
                            <v-btn color="error" @click.stop="deleteTask(task.id)">Delete</v-btn>
                        </td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>
            <v-btn class="my-5" absolute right color="primary" @click.stop="createTask">Add New Task</v-btn>
        </v-main>
        <v-footer
            color="indigo"
            app
        >
            <span class="white--text">&copy; {{ new Date().getFullYear() }}</span>
        </v-footer>

        <!--    Add/Edit task taskDialog(Modal)    -->
        <v-dialog v-model="taskDialog" scrollable max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">{{editMode ? 'Edit Task: ' + activeTask.title : 'Add New Task'}}</span>
                </v-card-title>
                <v-divider/>
                <v-card-text>
                    <v-container>
                        <v-form
                            ref="taskForm"
                            v-model="valid"
                            :lazy-validation="lazy">
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="activeTask.title"
                                        name="title"
                                        label="Title*"
                                        required
                                        :rules="[v => !!v || 'Email is required']"
                                    />
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea v-model="activeTask.description" name="description"
                                                label="Description"/>
                                </v-col>
                                <v-col cols="12">
                                    <v-menu
                                        ref="startDateMenu"
                                        v-model="startDateMenu"
                                        :close-on-content-click="false"
                                        :return-value.sync="activeTask.startDate"
                                        transition="scale-transition"
                                        offset-y
                                        min-width="290px"
                                    >
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-text-field
                                                v-model="activeTask.start_date"
                                                label="Start Date*"
                                                readonly
                                                v-bind="attrs"
                                                v-on="on"
                                                required
                                                name="start_date"
                                                :rules="[v => !!v || 'Start date is required']"
                                            />
                                        </template>
                                        <v-date-picker v-model="activeTask.start_date" no-title scrollable>
                                            <v-spacer/>
                                            <v-btn text color="primary" @click="startDateMenu = false">Cancel</v-btn>
                                            <v-btn text color="primary"
                                                   @click="$refs.startDateMenu.save(activeTask.start_date)">
                                                OK
                                            </v-btn>
                                        </v-date-picker>
                                    </v-menu>
                                </v-col>
                                <v-col cols="12">
                                    <v-menu
                                        ref="dueDateMenu"
                                        v-model="dueDateMenu"
                                        :close-on-content-click="false"
                                        :return-value.sync="activeTask.due_date"
                                        transition="scale-transition"
                                        offset-y
                                        min-width="290px"
                                    >
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-text-field
                                                v-model="activeTask.due_date"
                                                label="Due Date"
                                                readonly
                                                v-bind="attrs"
                                                v-on="on"
                                                name="due_date"
                                            />
                                        </template>
                                        <v-date-picker v-model="activeTask.due_date" no-title scrollable>
                                            <v-spacer/>
                                            <v-btn text color="primary" @click="dueDateMenu = false">Cancel</v-btn>
                                            <v-btn text color="primary"
                                                   @click="$refs.dueDateMenu.save(activeTask.due_date)">
                                                OK
                                            </v-btn>
                                        </v-date-picker>
                                    </v-menu>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        :items="defaultStatus"
                                        label="Status*"
                                        v-model="activeTask.status"
                                        required
                                        :rules="[v => !!v || 'Status is required']"
                                    />
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-container>
                    <small>*indicates required field</small>
                </v-card-text>
                <v-divider/>
                <v-card-actions>
                    <v-spacer/>
                    <v-btn color="blue darken-1" text @click="taskDialog = false">Close</v-btn>
                    <v-btn color="blue darken-1" text @click.stop="saveTask">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!--    Task delete confirmation taskDialog(Modal)    -->
        <v-dialog
            v-model="deleteTaskConfirmationModal"
            width="500"
        >
            <v-card>
                <v-card-text class="pa-5">
                    Are you sure you want to delete this task?
                </v-card-text>
                <v-divider/>
                <v-card-actions>
                    <v-spacer/>
                    <v-btn
                        color="primary"
                        text
                        @click="deleteTaskConfirmationModal = false"
                    >
                        No
                    </v-btn>
                    <v-btn
                        color="primary"
                        text
                        @click.stop="deleteTask"
                    >
                        Yes
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!--    Snackbar(Toast) to show message(Error,Success etc.)    -->
        <v-snackbar
            v-model="snackbar"
            top
            right
        >
            {{ message }}
            <template v-slot:action="{ attrs }">
                <v-btn
                    color="blue"
                    text
                    v-bind="attrs"
                    @click="snackbar = false"
                >
                    Close
                </v-btn>
            </template>
        </v-snackbar>
    </div>
</template>

<script>
    const API_BASE_URL = 'http://127.0.0.1:8000/api';

    export default {
        name: "Dashboard",
        data: () => ({
            tasks: [],
            activeTask: {},
            menuItems: [
                {icon: 'mdi-home', title: 'Home', link: '/'},
            ],
            defaultStatus: ['Pending', 'In Progress', 'Finished'],

            primaryNavigationDrawer: null,
            taskDialog: false,
            deleteTaskConfirmationModal: false,
            startDateMenu: null,
            dueDateMenu: null,
            finishedAtMenu: null,

            editMode: false,
            valid: true,
            lazy: true,
            snackbar: false,
            message: '',
        }),
        mounted() {
            this.loadTasks();
        },
        methods: {
            editTask(task) {
                this.editMode = true;
                this.activeTask = Object.assign({}, task);
                this.taskDialog = true;
            },

            createTask() {
                this.editMode = false;
                this.activeTask = {};
                this.taskDialog = true;
            },

            // API CALLS

            async loadTasks() {
                this.$axios.get(API_BASE_URL + '/tasks')
                    .then((res) => {
                        this.tasks = res.data.data;
                    }).catch(error => {
                    this.message = error.response.data.message;
                    this.snackbar = true;
                });
            },

            async saveTask() {
                try {
                    const isValidate = this.$refs.taskForm.validate();
                    if (isValidate) {
                        let response = {};
                        if (this.activeTask.id) {
                            response = await this.$axios.put(API_BASE_URL + '/tasks/' + this.activeTask.id, this.activeTask)
                        } else {
                            response = await this.$axios.post(API_BASE_URL + '/tasks', this.activeTask);
                        }
                        this.message = response.data.message;
                        this.activeTask = {};
                        this.taskDialog = false;
                        this.snackbar = true;
                        this.editMode = false;
                        await this.loadTasks();
                    }
                } catch (e) {
                    if (e.response) {
                        this.message = e.response.data.message;
                    } else {
                        this.message = 'Something went wrong';
                    }
                    this.snackbar = true;
                }
            },

            async deleteTask(id) {
                const res = await this.$confirm('Do you really want to delete this task?', {title: 'Warning'});
                if (res) {
                    this.$axios.delete(API_BASE_URL + '/tasks/' + id)
                        .then((response) => {
                            this.loadTasks();
                            this.message = response.data.message;
                            this.snackbar = true;
                        }).catch(error => {
                        this.message = error.response.data.message;
                        this.snackbar = true;
                    }).finally(() => {
                        this.deleteTaskConfirmationModal = false;
                    });
                }
            },
        }
    }
</script>
