<!-- Main Content -->
<main class="min-h-screen bg-brand-bg">
    <!-- Breadcrumb -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-primary/10">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center gap-2 text-sm">
                <a href="<?php echo base_url(); ?>" class="text-gray hover:text-primary transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <a href="<?php echo base_url('account/profile'); ?>" class="text-gray hover:text-primary transition-colors">My Account</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <span class="text-dark font-medium">My Addresses</span>
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
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-dark">My Addresses</h1>
                        <p class="text-gray text-sm mt-1">Save addresses for faster checkout</p>
                    </div>
                    <button id="addNewAddressBtn" class="btn-primary text-sm py-2.5 whitespace-nowrap">
                        <i class="fa-solid fa-plus mr-1.5"></i>
                        Add New Address
                    </button>
                </div>

                <?php if (!empty($addresses)): ?>
                <!-- Addresses Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="addressesList">
                    <?php foreach ($addresses as $address): ?>
                    <div class="address-card bg-white rounded-3xl shadow-md shadow-primary/5 overflow-hidden relative border border-primary/5" data-address-id="<?php echo $address->id; ?>">
                        <?php if ($address->is_default == 1): ?>
                        <span class="absolute top-3 right-3 px-2 py-0.5 bg-primary text-white text-xs font-semibold rounded-full">Default</span>
                        <?php endif; ?>

                        <div class="p-5">
                            <div class="flex items-start gap-3 mb-4">
                                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                    <?php if ($address->address_type == 'home'): ?>
                                    <i class="fa-solid fa-house text-primary"></i>
                                    <?php elseif ($address->address_type == 'work'): ?>
                                    <i class="fa-solid fa-briefcase text-primary"></i>
                                    <?php else: ?>
                                    <i class="fa-solid fa-location-dot text-primary"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-dark mb-1"><?php echo ucfirst($address->address_type); ?></h4>
                                    <p class="text-sm text-dark font-medium"><?php echo htmlspecialchars($address->fullname); ?></p>
                                </div>
                            </div>

                            <p class="text-sm text-gray leading-relaxed mb-3">
                                <?php echo htmlspecialchars($address->address_line1); ?>
                                <?php if (!empty($address->address_line2)): ?>, <?php echo htmlspecialchars($address->address_line2); ?><?php endif; ?>,<br>
                                <?php echo htmlspecialchars($address->city); ?>, <?php echo htmlspecialchars($address->state); ?> - <?php echo htmlspecialchars($address->pincode); ?>
                            </p>

                            <p class="text-sm text-dark mb-4">
                                <i class="fa-solid fa-phone text-xs mr-1 text-gray"></i>
                                +91 <?php echo htmlspecialchars($address->phone); ?>
                            </p>

                            <!-- Actions -->
                            <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                                <button class="edit-address-btn text-sm text-primary font-medium hover:underline" data-address='<?php echo json_encode($address); ?>'>
                                    <i class="fa-solid fa-pen mr-1"></i>
                                    Edit
                                </button>
                                <?php if ($address->is_default != 1): ?>
                                <span class="text-gray-300">|</span>
                                <button class="set-default-btn text-sm text-primary font-medium hover:underline" data-address-id="<?php echo $address->id; ?>">
                                    <i class="fa-solid fa-check mr-1"></i>
                                    Set as Default
                                </button>
                                <span class="text-gray-300">|</span>
                                <button class="delete-address-btn text-sm text-red-500 font-medium hover:underline" data-address-id="<?php echo $address->id; ?>">
                                    <i class="fa-solid fa-trash mr-1"></i>
                                    Delete
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <!-- Empty State -->
                <div class="bg-white rounded-3xl shadow-md shadow-primary/5 p-8 text-center border border-primary/5">
                    <div class="w-24 h-24 bg-light-gray rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-4xl">📍</span>
                    </div>
                    <h2 class="text-xl font-bold text-dark mb-2">No addresses saved yet</h2>
                    <p class="text-gray mb-6">Add your first address for faster PIKO POP checkout.</p>
                    <button id="addFirstAddressBtn" class="btn-primary inline-flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add New Address</span>
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<!-- Add/Edit Address Modal -->
<div id="addressModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="address-modal-backdrop absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <!-- Modal Content -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="address-modal-content bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-hidden shadow-2xl transform scale-95 opacity-0 transition-all duration-300">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 id="addressModalTitle" class="text-lg font-bold text-dark">Add New Address</h3>
                <button id="closeAddressModal" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray hover:text-dark transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 overflow-y-auto max-h-[calc(90vh-140px)]">
            <form id="addressForm" class="space-y-4">
                <input type="hidden" name="address_id" id="addressId" value="">

                <!-- Address Type -->
                <div>
                    <label class="block text-sm font-medium text-dark mb-3">Address Type</label>
                    <div class="flex gap-3">
                        <label class="flex-1">
                            <input type="radio" name="address_type" value="home" class="sr-only peer" checked>
                            <div class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                <i class="fa-solid fa-house text-gray peer-checked:text-primary"></i>
                                <span class="font-medium">Home</span>
                            </div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="address_type" value="work" class="sr-only peer">
                            <div class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                <i class="fa-solid fa-briefcase text-gray peer-checked:text-primary"></i>
                                <span class="font-medium">Work</span>
                            </div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="address_type" value="other" class="sr-only peer">
                            <div class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                <i class="fa-solid fa-location-dot text-gray peer-checked:text-primary"></i>
                                <span class="font-medium">Other</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Full Name *</label>
                        <input type="text" name="fullname" id="addrFullName" placeholder="Enter full name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Phone Number *</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-dark font-medium text-sm">+91</span>
                            <input type="tel" name="phone" id="addrPhone" maxlength="10" placeholder="Enter phone" class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all" required>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark mb-2">Address Line 1 *</label>
                    <input type="text" name="address_line1" id="addrLine1" placeholder="House/Flat No., Building Name, Street" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark mb-2">Address Line 2</label>
                    <input type="text" name="address_line2" id="addrLine2" placeholder="Area, Colony, Landmark (Optional)" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">City *</label>
                        <input type="text" name="city" id="addrCity" placeholder="City" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">State *</label>
                        <select name="state" id="addrState" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all appearance-none cursor-pointer" required>
                            <option value="">Select</option>
                            <option value="Andhra Pradesh">Andhra Pradesh</option>
                            <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                            <option value="Assam">Assam</option>
                            <option value="Bihar">Bihar</option>
                            <option value="Chhattisgarh">Chhattisgarh</option>
                            <option value="Delhi">Delhi</option>
                            <option value="Goa">Goa</option>
                            <option value="Gujarat">Gujarat</option>
                            <option value="Haryana">Haryana</option>
                            <option value="Himachal Pradesh">Himachal Pradesh</option>
                            <option value="Jharkhand">Jharkhand</option>
                            <option value="Karnataka">Karnataka</option>
                            <option value="Kerala">Kerala</option>
                            <option value="Madhya Pradesh">Madhya Pradesh</option>
                            <option value="Maharashtra">Maharashtra</option>
                            <option value="Manipur">Manipur</option>
                            <option value="Meghalaya">Meghalaya</option>
                            <option value="Mizoram">Mizoram</option>
                            <option value="Nagaland">Nagaland</option>
                            <option value="Odisha">Odisha</option>
                            <option value="Punjab">Punjab</option>
                            <option value="Rajasthan">Rajasthan</option>
                            <option value="Sikkim">Sikkim</option>
                            <option value="Tamil Nadu">Tamil Nadu</option>
                            <option value="Telangana">Telangana</option>
                            <option value="Tripura">Tripura</option>
                            <option value="Uttar Pradesh">Uttar Pradesh</option>
                            <option value="Uttarakhand">Uttarakhand</option>
                            <option value="West Bengal">West Bengal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Pincode *</label>
                        <input type="text" name="pincode" id="addrPincode" maxlength="6" placeholder="Pincode" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all" required>
                    </div>
                </div>

                <!-- Set as Default -->
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_default" id="addrDefault" class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary">
                    <label for="addrDefault" class="text-sm font-medium text-dark cursor-pointer">Set as default address</label>
                </div>
            </form>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-gray-100 bg-gray-50">
                <button type="button" id="cancelAddressBtn" class="px-6 py-2.5 border border-gray-200 rounded-full font-medium text-dark hover:bg-white transition-all">
                    Cancel
                </button>
                <button type="submit" form="addressForm" id="saveAddressBtn" class="btn-primary px-6 py-2.5">
                    <i class="fa-solid fa-check mr-2"></i>
                    <span id="saveAddressBtnText">Save Address</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="delete-modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

    <!-- Modal Container -->
    <div class="flex min-h-full items-center justify-center p-4">
        <!-- Modal Content -->
        <div class="delete-modal-content relative w-full max-w-sm bg-white rounded-2xl shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
            <!-- Drag Handle (Mobile Only) -->
            <div class="flex justify-center pt-3 pb-1 md:hidden">
                <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 text-center">
                <!-- Icon -->
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-trash-can text-2xl text-red-500"></i>
                </div>

                <h3 class="text-xl font-bold text-dark mb-2">Delete Address?</h3>
                <p class="text-gray mb-1">Are you sure you want to delete</p>
                <p class="font-semibold text-dark mb-6">"<span id="deleteAddressType">Home</span>" address?</p>

                <input type="hidden" id="deleteAddressId" value="">

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <button id="cancelDeleteBtn" class="flex-1 px-4 py-3 border border-gray-200 rounded-full font-semibold text-dark hover:bg-gray-50 transition-all">
                        Cancel
                    </button>
                    <button id="confirmDeleteBtn" class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-full font-semibold transition-all">
                        <i class="fa-solid fa-trash-can mr-2"></i>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
