<!-- Main Content -->
<div class="site-page">
    <!-- Breadcrumb -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-primary/10">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center gap-2 text-sm">
                <a href="<?php echo base_url(); ?>" class="text-gray hover:text-primary transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <span class="text-dark font-medium">My Profile</span>
            </nav>
        </div>
    </div>

    <!-- Dashboard Section -->
    <div class="container mx-auto px-4 py-6 md:py-10">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 lg:gap-8">

            <!-- Sidebar -->
            <?php $this->load->view('user/account/sidebar'); ?>

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Page Title -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-dark">My Profile</h1>
                        <p class="text-gray text-sm mt-1">Manage your PIKO POP account details</p>
                    </div>
                </div>

                <!-- Profile Information Card -->
                <div class="bg-white rounded-3xl shadow-md shadow-primary/5 overflow-hidden border border-primary/5">
                    <div class="flex items-center justify-between px-5 md:px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-user text-primary"></i>
                            </div>
                            <h2 class="text-lg font-bold text-dark">Personal Information</h2>
                        </div>
                        <button id="editProfileBtn" class="text-primary font-medium text-sm hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-pen text-xs"></i>
                            <span>Edit</span>
                        </button>
                    </div>

                    <div class="p-5 md:p-6">
                        <!-- View Mode -->
                        <div id="profileView" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm text-gray mb-1">Full Name</label>
                                <p class="font-semibold text-dark"><?php echo htmlspecialchars($user->fullname); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray mb-1">Email Address</label>
                                <p class="font-semibold text-dark"><?php echo htmlspecialchars($user->email); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray mb-1">Phone Number</label>
                                <p class="font-semibold text-dark"><?php echo !empty($user->phone) ? '+91 ' . htmlspecialchars($user->phone) : 'Not provided'; ?></p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray mb-1">Gender</label>
                                <p class="font-semibold text-dark"><?php echo !empty($user->gender) ? ucfirst($user->gender) : 'Not provided'; ?></p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray mb-1">Date of Birth</label>
                                <p class="font-semibold text-dark"><?php echo !empty($user->date_of_birth) ? date('d F Y', strtotime($user->date_of_birth)) : 'Not provided'; ?></p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray mb-1">Member Since</label>
                                <p class="font-semibold text-dark"><?php echo date('F Y', strtotime($user->created_at)); ?></p>
                            </div>
                        </div>

                        <!-- Edit Mode (Hidden by default) -->
                        <form id="profileEditForm" class="hidden space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-2">Full Name *</label>
                                    <input type="text" name="fullname" value="<?php echo htmlspecialchars($user->fullname); ?>" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-2">Email Address *</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($user->email); ?>" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-2">Phone Number</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-dark font-medium text-sm">+91</span>
                                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user->phone ?? ''); ?>" maxlength="10" class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-2">Gender</label>
                                    <select name="gender" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all appearance-none cursor-pointer">
                                        <option value="">Select Gender</option>
                                        <option value="male" <?php echo (isset($user->gender) && $user->gender == 'male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo (isset($user->gender) && $user->gender == 'female') ? 'selected' : ''; ?>>Female</option>
                                        <option value="other" <?php echo (isset($user->gender) && $user->gender == 'other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-2">Date of Birth</label>
                                    <input type="date" name="date_of_birth" value="<?php echo $user->date_of_birth ?? ''; ?>" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all">
                                </div>
                            </div>
                            <div class="flex items-center gap-3 pt-4">
                                <button type="submit" class="btn-primary" id="saveProfileBtn">
                                    <i class="fa-solid fa-check mr-2"></i>
                                    Save Changes
                                </button>
                                <button type="button" id="cancelEditBtn" class="px-6 py-3 border border-gray-200 rounded-full font-semibold text-dark hover:bg-gray-50 transition-all">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Saved Addresses Card -->
                <div class="bg-white rounded-3xl shadow-md shadow-primary/5 overflow-hidden border border-primary/5">
                    <div class="flex items-center justify-between px-5 md:px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-secondary/10 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-location-dot text-secondary"></i>
                            </div>
                            <h2 class="text-lg font-bold text-dark">Default Address</h2>
                        </div>
                        <a href="<?php echo base_url('account/addresses'); ?>" class="text-primary font-medium text-sm hover:underline flex items-center gap-1">
                            <span>Manage Addresses</span>
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </a>
                    </div>

                    <div class="p-5 md:p-6">
                        <?php if (!empty($default_address)): ?>
                        <div class="relative border border-primary rounded-xl p-4">
                            <span class="absolute top-3 right-3 px-2 py-0.5 bg-primary text-white text-xs font-semibold rounded-full">Default</span>
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <?php if ($default_address->address_type == 'home'): ?>
                                    <i class="fa-solid fa-house text-primary text-sm"></i>
                                    <?php elseif ($default_address->address_type == 'work'): ?>
                                    <i class="fa-solid fa-briefcase text-primary text-sm"></i>
                                    <?php else: ?>
                                    <i class="fa-solid fa-location-dot text-primary text-sm"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-dark mb-1"><?php echo ucfirst($default_address->address_type); ?></h4>
                                    <p class="text-sm text-gray leading-relaxed">
                                        <?php echo htmlspecialchars($default_address->fullname); ?><br>
                                        <?php echo htmlspecialchars($default_address->address_line1); ?>
                                        <?php if (!empty($default_address->address_line2)): ?>, <?php echo htmlspecialchars($default_address->address_line2); ?><?php endif; ?>,<br>
                                        <?php echo htmlspecialchars($default_address->city); ?>, <?php echo htmlspecialchars($default_address->state); ?> - <?php echo htmlspecialchars($default_address->pincode); ?>
                                    </p>
                                    <p class="text-sm text-dark mt-2">
                                        <i class="fa-solid fa-phone text-xs mr-1"></i>
                                        +91 <?php echo htmlspecialchars($default_address->phone); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-location-dot text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-gray mb-4">No default address set</p>
                            <a href="<?php echo base_url('account/addresses'); ?>" class="btn-primary inline-flex items-center gap-2">
                                <i class="fa-solid fa-plus"></i>
                                <span>Add Address</span>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Account Settings Card -->
                <!-- <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="flex items-center gap-3 px-5 md:px-6 py-4 border-b border-gray-100">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-gear text-gray"></i>
                        </div>
                        <h2 class="text-lg font-bold text-dark">Account Settings</h2>
                    </div>

                    <div class="divide-y divide-gray-100">
                        <button id="changePasswordBtn" class="w-full flex items-center justify-between px-5 md:px-6 py-4 hover:bg-gray-50 transition-colors text-left">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-lock w-5 text-center text-gray"></i>
                                <span class="font-medium text-dark">Change Password</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-gray text-sm"></i>
                        </button>
                    </div>
                </div> -->
            </div>
        </div>
    </div>

<!-- Change Password Modal -->
<div id="passwordModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="password-modal-backdrop absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <!-- Modal Content -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="password-modal-content bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl transform scale-95 opacity-0 transition-all duration-300">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-dark">Change Password</h3>
                <button id="closePasswordModal" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray hover:text-dark transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="passwordForm" class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-dark mb-2">Current Password *</label>
                    <input type="password" name="current_password" placeholder="Enter current password" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark mb-2">New Password *</label>
                    <input type="password" name="new_password" placeholder="Enter new password" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all" required minlength="6">
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark mb-2">Confirm New Password *</label>
                    <input type="password" name="confirm_password" placeholder="Confirm new password" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all" required minlength="6">
                </div>
            </form>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-gray-100 bg-gray-50">
                <button type="button" id="cancelPasswordBtn" class="px-6 py-2.5 border border-gray-200 rounded-full font-medium text-dark hover:bg-white transition-all">
                    Cancel
                </button>
                <button type="submit" form="passwordForm" id="savePasswordBtn" class="btn-primary px-6 py-2.5">
                    <i class="fa-solid fa-check mr-2"></i>
                    Update Password
                </button>
            </div>
        </div>
    </div>
</div>
