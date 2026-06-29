<!-- Main Content -->
<main class="flex-1 flex items-center justify-center py-8 px-4 bg-brand-bg min-h-[calc(100vh-80px)]">
    <div class="w-full max-w-md">
        <!-- Login Card -->
        <div class="card-pop p-6 md:p-8">

            <!-- Step 1: Mobile Number -->
            <div id="stepMobile" class="login-step">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-primary/15 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-mobile-screen text-3xl text-primary"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-dark mb-2">Welcome to PIKO POP!</h1>
                    <p class="text-gray text-sm">Enter your mobile number to start shopping cute things</p>
                    <?php if (!empty($otp_bypass_enabled)): ?>
                    <p class="mt-3 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                        Test login: after Get OTP, use <strong><?php echo htmlspecialchars($otp_bypass_code); ?></strong>
                    </p>
                    <?php endif; ?>
                </div>

                <form id="mobileForm" class="space-y-5">
                    <div>
                        <label class="block text-dark font-medium mb-2">Mobile Number</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 bg-gray-100 border border-r-0 border-gray-200 rounded-l-lg text-dark font-medium">
                                +91
                            </span>
                            <input
                                type="tel"
                                id="mobileNumber"
                                placeholder="Enter 10 digit mobile number"
                                maxlength="10"
                                pattern="[0-9]{10}"
                                class="flex-1 px-4 py-3 border border-gray-200 rounded-r-lg focus:outline-none focus:border-primary transition-colors text-lg tracking-wider"
                                required
                            >
                        </div>
                        <p id="mobileError" class="text-red-500 text-sm mt-1 hidden">Please enter a valid 10 digit mobile number</p>
                    </div>

                    <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white py-4 rounded-full font-semibold text-base transition-all flex items-center justify-center gap-2">
                        <span>Get OTP</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <p class="text-center text-gray text-sm mt-6">
                    By continuing, you agree to our
                    <a href="<?php echo base_url('terms'); ?>" class="text-primary hover:underline">Terms of Service</a>
                    and
                    <a href="<?php echo base_url('privacy'); ?>" class="text-primary hover:underline">Privacy Policy</a>
                </p>
            </div>

            <!-- Step 2: OTP Verification -->
            <div id="stepOTP" class="login-step hidden">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-secondary/15 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-shield-check text-3xl text-secondary"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-dark mb-2">Verify OTP</h1>
                    <p class="text-gray text-sm">We've sent a 6-digit code to</p>
                    <p class="text-dark font-semibold mt-1">+91 <span id="displayMobile"></span></p>
                </div>

                <form id="otpForm" class="space-y-5">
                    <div>
                        <label class="block text-dark font-medium mb-3">Enter OTP</label>
                        <div class="flex gap-2 justify-center" id="otpInputs">
                            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-bold border-2 border-gray-200 rounded-lg focus:outline-none focus:border-primary transition-colors" required>
                            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-bold border-2 border-gray-200 rounded-lg focus:outline-none focus:border-primary transition-colors" required>
                            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-bold border-2 border-gray-200 rounded-lg focus:outline-none focus:border-primary transition-colors" required>
                            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-bold border-2 border-gray-200 rounded-lg focus:outline-none focus:border-primary transition-colors" required>
                            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-bold border-2 border-gray-200 rounded-lg focus:outline-none focus:border-primary transition-colors" required>
                            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-bold border-2 border-gray-200 rounded-lg focus:outline-none focus:border-primary transition-colors" required>
                        </div>
                        <p id="otpError" class="text-red-500 text-sm mt-2 text-center hidden">Invalid OTP. Please try again.</p>
                    </div>

                    <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white py-4 rounded-full font-semibold text-base transition-all">
                        Verify & Continue
                    </button>
                </form>

                <div class="text-center mt-6">
                    <p class="text-gray text-sm mb-2">Didn't receive the code?</p>
                    <button id="resendOTP" class="text-primary font-semibold hover:underline disabled:text-gray disabled:no-underline" disabled>
                        Resend OTP <span id="resendTimer">(30s)</span>
                    </button>
                </div>

                <button id="backToMobile" class="w-full mt-4 text-gray hover:text-dark text-sm font-medium flex items-center justify-center gap-2 transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Change Mobile Number</span>
                </button>
            </div>

            <!-- Step 3: New User Registration -->
            <div id="stepRegister" class="login-step hidden">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-primary/15 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-user-plus text-3xl text-primary"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-dark mb-2">Almost There!</h1>
                    <p class="text-gray text-sm">Please enter your details to complete registration</p>
                </div>

                <form id="registerForm" class="space-y-5">
                    <div>
                        <label class="block text-dark font-medium mb-2">Full Name</label>
                        <input
                            type="text"
                            id="fullName"
                            placeholder="Enter your full name"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-primary transition-colors"
                            required
                        >
                        <p id="nameError" class="text-red-500 text-sm mt-1 hidden">Please enter your name</p>
                    </div>

                    <div>
                        <label class="block text-dark font-medium mb-2">Email Address <span class="text-gray font-normal">(Optional)</span></label>
                        <input
                            type="email"
                            id="emailAddress"
                            placeholder="Enter your email address"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-primary transition-colors"
                        >
                    </div>

                    <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white py-4 rounded-full font-semibold text-base transition-all flex items-center justify-center gap-2">
                        <span>Create Account</span>
                        <i class="fa-solid fa-check"></i>
                    </button>
                </form>
            </div>

            <!-- Success State -->
            <div id="stepSuccess" class="login-step hidden">
                <div class="text-center py-8">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-check text-4xl text-green-500"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-dark mb-2">Yay, you're in!</h1>
                    <p class="text-gray text-sm mb-6">Ready to shop something cute?</p>
                    <p class="text-gray text-sm">Redirecting...</p>
                </div>
            </div>

        </div>

        <!-- Help Link -->
        <p class="text-center text-gray text-sm mt-6">
            Need help?
            <a href="<?php echo base_url('page/contact'); ?>" class="text-primary hover:underline">Contact Support</a>
        </p>
    </div>
</main>
