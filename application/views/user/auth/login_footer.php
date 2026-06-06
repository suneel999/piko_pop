<!-- Footer (Minimal) -->
<footer class="py-4 text-center mt-auto bg-secondary">
    <p class="text-white/70 text-sm">&copy; <?php echo date('Y'); ?> PIKO POP. All rights reserved.</p>
</footer>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Base URL for JS -->
<script>
    var base_url = '<?php echo base_url(); ?>';
</script>

<!-- Login Page Script -->
<script>
$(document).ready(function() {
    'use strict';

    let mobileNumber = '';
    let resendCountdown;

    // ========================================
    // Step 1: Mobile Number Submission
    // ========================================
    $('#mobileForm').on('submit', function(e) {
        e.preventDefault();

        mobileNumber = $('#mobileNumber').val().trim();

        // Validate mobile number (Indian mobile: starts with 6-9)
        if (!/^[6-9]\d{9}$/.test(mobileNumber)) {
            $('#mobileError').removeClass('hidden');
            return;
        }

        $('#mobileError').addClass('hidden');

        // Show loading state
        const $btn = $(this).find('button[type="submit"]');
        const originalText = $btn.html();
        $btn.html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Sending OTP...').prop('disabled', true);

        // Send OTP via AJAX
        $.ajax({
            url: base_url + 'login/send_otp',
            type: 'POST',
            data: { phone: mobileNumber },
            dataType: 'json',
            success: function(response) {
                $btn.html(originalText).prop('disabled', false);

                if (response.status == 200) {
                    // Update display and show OTP step
                    $('#displayMobile').text(mobileNumber);
                    showStep('stepOTP');

                    // Start resend timer
                    startResendTimer();

                    // Focus first OTP input
                    $('.otp-input').first().focus();
                } else {
                    $('#mobileError').text(response.message).removeClass('hidden');
                }
            },
            error: function() {
                $btn.html(originalText).prop('disabled', false);
                $('#mobileError').text('Something went wrong. Please try again.').removeClass('hidden');
            }
        });
    });

    // ========================================
    // Step 2: OTP Verification
    // ========================================

    // OTP Input handling - auto focus next input
    $(document).on('input', '.otp-input', function() {
        const $this = $(this);
        const val = $this.val();

        // Only allow numbers
        $this.val(val.replace(/[^0-9]/g, ''));

        // Auto focus next input
        if (val.length === 1) {
            $this.next('.otp-input').focus();
        }
    });

    // Handle backspace
    $(document).on('keydown', '.otp-input', function(e) {
        if (e.key === 'Backspace' && $(this).val() === '') {
            $(this).prev('.otp-input').focus();
        }
    });

    // Handle paste
    $('.otp-input').first().on('paste', function(e) {
        e.preventDefault();
        const pastedData = e.originalEvent.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);

        $('.otp-input').each(function(index) {
            $(this).val(pastedData[index] || '');
        });

        $('.otp-input').last().focus();
    });

    // OTP Form submission
    $('#otpForm').on('submit', function(e) {
        e.preventDefault();

        // Get OTP value
        let otp = '';
        $('.otp-input').each(function() {
            otp += $(this).val();
        });

        // Validate OTP length
        if (otp.length !== 6) {
            $('#otpError').removeClass('hidden').text('Please enter complete 6-digit OTP');
            return;
        }

        $('#otpError').addClass('hidden');

        // Show loading state
        const $btn = $(this).find('button[type="submit"]');
        const originalText = $btn.html();
        $btn.html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Verifying...').prop('disabled', true);

        // Verify OTP via AJAX
        $.ajax({
            url: base_url + 'login/verify_otp',
            type: 'POST',
            data: {
                phone: mobileNumber,
                otp: otp
            },
            dataType: 'json',
            success: function(response) {
                $btn.html(originalText).prop('disabled', false);

                if (response.status == 200) {
                    if (response.is_new_user == 1) {
                        // New user - show registration form
                        showStep('stepRegister');
                        $('#fullName').focus();
                    } else {
                        // Existing user - show success and redirect
                        showStep('stepSuccess');
                        setTimeout(function() {
                            window.location.href = response.redirect || base_url;
                        }, 1500);
                    }
                } else {
                    $('#otpError').removeClass('hidden').text(response.message);
                    $('.otp-input').val('').first().focus();
                }
            },
            error: function() {
                $btn.html(originalText).prop('disabled', false);
                $('#otpError').removeClass('hidden').text('Something went wrong. Please try again.');
            }
        });
    });

    // Resend OTP
    $('#resendOTP').on('click', function() {
        if ($(this).prop('disabled')) return;

        const $btn = $(this);
        $btn.html('<i class="fa-solid fa-spinner fa-spin"></i>').prop('disabled', true);

        // Resend OTP via AJAX
        $.ajax({
            url: base_url + 'login/resend_otp',
            type: 'POST',
            data: { phone: mobileNumber },
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    $btn.html('Resend OTP <span id="resendTimer">(30s)</span>');
                    startResendTimer();

                    // Clear OTP inputs
                    $('.otp-input').val('').first().focus();
                } else {
                    $btn.html('Resend OTP').prop('disabled', false);
                    alert(response.message);
                }
            },
            error: function() {
                $btn.html('Resend OTP').prop('disabled', false);
                alert('Failed to resend OTP. Please try again.');
            }
        });
    });

    // Back to mobile number
    $('#backToMobile').on('click', function() {
        clearInterval(resendCountdown);
        showStep('stepMobile');
        $('#mobileNumber').focus();
    });

    // ========================================
    // Step 3: Registration
    // ========================================
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();

        const fullName = $('#fullName').val().trim();
        const email = $('#emailAddress').val().trim();

        // Validate name
        if (fullName.length < 2) {
            $('#nameError').removeClass('hidden').text('Please enter a valid name');
            return;
        }

        $('#nameError').addClass('hidden');

        // Show loading state
        const $btn = $(this).find('button[type="submit"]');
        const originalText = $btn.html();
        $btn.html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Creating Account...').prop('disabled', true);

        // Register via AJAX
        $.ajax({
            url: base_url + 'login/register',
            type: 'POST',
            data: {
                phone: mobileNumber,
                fullname: fullName,
                email: email
            },
            dataType: 'json',
            success: function(response) {
                $btn.html(originalText).prop('disabled', false);

                if (response.status == 200) {
                    // Show success and redirect
                    showStep('stepSuccess');
                    setTimeout(function() {
                        window.location.href = response.redirect || base_url;
                    }, 1500);
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                $btn.html(originalText).prop('disabled', false);
                alert('Something went wrong. Please try again.');
            }
        });
    });

    // ========================================
    // Helper Functions
    // ========================================
    function showStep(stepId) {
        $('.login-step').addClass('hidden');
        $('#' + stepId).removeClass('hidden');
    }

    function startResendTimer() {
        let seconds = 30;
        const $resendBtn = $('#resendOTP');
        const $timer = $('#resendTimer');

        $resendBtn.prop('disabled', true);

        resendCountdown = setInterval(function() {
            seconds--;
            $timer.text('(' + seconds + 's)');

            if (seconds <= 0) {
                clearInterval(resendCountdown);
                $resendBtn.prop('disabled', false);
                $timer.text('');
            }
        }, 1000);
    }

    // Only allow numbers in mobile input
    $('#mobileNumber').on('input', function() {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
        $('#mobileError').addClass('hidden');
    });
});
</script>
</body>

</html>
