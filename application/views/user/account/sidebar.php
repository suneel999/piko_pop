<!-- Account Sidebar -->
<div class="lg:col-span-1">
    <div class="bg-white rounded-3xl shadow-md shadow-primary/5 overflow-hidden sticky top-24 border border-primary/5">
        <!-- User Info -->
        <div class="p-5 bg-gradient-to-r from-primary/5 to-secondary/5 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-primary/20 flex items-center justify-center">
                    <?php
                        $initials = '';
                        if (!empty($user->fullname)) {
                            $name_parts = explode(' ', $user->fullname);
                            foreach ($name_parts as $part) {
                                $initials .= strtoupper(substr($part, 0, 1));
                            }
                            $initials = substr($initials, 0, 2);
                        } else {
                            $initials = 'U';
                        }
                    ?>
                    <span class="text-primary font-bold text-xl"><?php echo $initials; ?></span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-dark truncate"><?php echo htmlspecialchars($user->fullname); ?></h3>
                    <p class="text-sm text-gray truncate"><?php echo htmlspecialchars($user->email); ?></p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="py-2">
            <a href="<?php echo base_url('account/profile'); ?>" class="flex items-center gap-3 px-5 py-3 <?php echo $active_page == 'profile' ? 'bg-primary/5 text-primary border-r-4 border-primary font-medium' : 'text-dark hover:bg-light-gray hover:text-primary transition-all'; ?>">
                <i class="fa-solid fa-user w-5 text-center <?php echo $active_page == 'profile' ? '' : 'text-gray'; ?>"></i>
                <span>My Profile</span>
            </a>
            <a href="<?php echo base_url('account/orders'); ?>" class="flex items-center gap-3 px-5 py-3 <?php echo $active_page == 'orders' || $active_page == 'order_detail' ? 'bg-primary/5 text-primary border-r-4 border-primary font-medium' : 'text-dark hover:bg-light-gray hover:text-primary transition-all'; ?>">
                <i class="fa-solid fa-box w-5 text-center <?php echo $active_page == 'orders' || $active_page == 'order_detail' ? '' : 'text-gray'; ?>"></i>
                <span>My PIKO Orders</span>
                <?php if (!empty($total_orders) && $total_orders > 0): ?>
                <span class="ml-auto bg-gray-100 text-gray text-xs font-semibold px-2 py-0.5 rounded-full"><?php echo $total_orders; ?></span>
                <?php endif; ?>
            </a>
            <a href="<?php echo base_url('account/addresses'); ?>" class="flex items-center gap-3 px-5 py-3 <?php echo $active_page == 'addresses' ? 'bg-primary/5 text-primary border-r-4 border-primary font-medium' : 'text-dark hover:bg-light-gray hover:text-primary transition-all'; ?>">
                <i class="fa-solid fa-location-dot w-5 text-center <?php echo $active_page == 'addresses' ? '' : 'text-gray'; ?>"></i>
                <span>My Addresses</span>
            </a>
            <a href="<?php echo base_url('wishlist'); ?>" class="flex items-center gap-3 px-5 py-3 <?php echo !empty($active_page) && $active_page == 'wishlist' ? 'bg-primary/5 text-primary border-r-4 border-primary font-medium' : 'text-dark hover:bg-light-gray hover:text-primary transition-all'; ?>">
                <i class="fa-solid fa-heart w-5 text-center <?php echo !empty($active_page) && $active_page == 'wishlist' ? '' : 'text-gray'; ?>"></i>
                <span>Wishlist</span>
                <?php if (!empty($wishlist_count) && $wishlist_count > 0): ?>
                <span class="ml-auto bg-secondary/10 text-secondary text-xs font-semibold px-2 py-0.5 rounded-full"><?php echo $wishlist_count; ?></span>
                <?php endif; ?>
            </a>
            <div class="border-t border-gray-100 my-2"></div>
            <a href="<?php echo base_url('logout'); ?>" class="flex items-center gap-3 px-5 py-3 text-red-500 hover:bg-red-50 transition-all">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>
</div>
