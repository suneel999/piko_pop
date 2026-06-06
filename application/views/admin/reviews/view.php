<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Review Detail</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('admin_root/reviews') ?>">Reviews</a></li>
                            <li class="breadcrumb-item active">Review #<?php echo $review->id ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <?php
            $rating = intval($review->overall_rating);
            $rating_labels = array(1 => 'Poor', 2 => 'Fair', 3 => 'Average', 4 => 'Good', 5 => 'Excellent');
            $rating_colors = array(1 => '#ef4444', 2 => '#f97316', 3 => '#eab308', 4 => '#22c55e', 5 => '#16a34a');
            $rating_label = isset($rating_labels[$rating]) ? $rating_labels[$rating] : '';
            $rating_color = isset($rating_colors[$rating]) ? $rating_colors[$rating] : '#eab308';
        ?>

        <!-- Rating Hero Banner -->
        <div class="row">
            <div class="col-12">
                <div class="card" style="background: linear-gradient(135deg, <?php echo $rating_color ?>15, <?php echo $rating_color ?>08); border-left: 4px solid <?php echo $rating_color ?>;">
                    <div class="card-body py-4">
                        <div class="row align-items-center">
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="me-4 text-center" style="min-width: 80px;">
                                    <h1 class="mb-0 fw-bold" style="font-size: 3rem; color: <?php echo $rating_color ?>;"><?php echo $rating ?></h1>
                                    <small class="text-muted">out of 5</small>
                                </div>
                                <div>
                                    <div class="mb-1">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span style="color: <?php echo $i <= $rating ? '#f59e0b' : '#e5e7eb' ?>; font-size: 1.5rem;">&#9733;</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="badge font-size-14 px-3 py-2" style="background: <?php echo $rating_color ?>20; color: <?php echo $rating_color ?>;"><?php echo $rating_label ?></span>
                                </div>
                            </div>
                            <?php $is_guest_review = empty($review->order_id); ?>
                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                <h5 class="mb-1"><?php echo !empty($review->customer_name) ? htmlspecialchars($review->customer_name) : (!empty($review->delivery_fullname) ? htmlspecialchars($review->delivery_fullname) : 'Guest') ?></h5>
                                <p class="text-muted mb-1">
                                    <?php if (!$is_guest_review): ?>
                                        <a href="tel:<?php echo $review->delivery_phone ?>" class="text-muted"><?php echo htmlspecialchars($review->delivery_phone) ?></a>
                                        &middot;
                                        Order <a href="<?php echo base_url('admin_root/orders/view/' . $review->order_id) ?>" class="text-primary fw-bold">#<?php echo htmlspecialchars($review->order_number) ?></a>
                                    <?php else: ?>
                                        <?php if (!empty($review->guest_phone)): ?>
                                            <a href="tel:<?php echo $review->guest_phone ?>" class="text-muted"><?php echo htmlspecialchars($review->guest_phone) ?></a> &middot;
                                        <?php endif; ?>
                                        <span class="badge bg-soft-secondary text-secondary">Guest Review</span>
                                    <?php endif; ?>
                                </p>
                                <small class="text-muted">Submitted on <?php echo date('d M Y, h:i A', strtotime($review->updated_at)) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Feedback Answers -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4"><i class="uil uil-clipboard-notes text-primary me-2"></i>Feedback Responses</h4>

                        <?php
                        $feedback_items = array(
                            array('icon' => '🍽️', 'label' => 'Taste Feedback', 'question' => 'How did you like the taste?', 'value' => $review->taste_feedback),
                            array('icon' => '👨‍🍳', 'label' => 'Ease of Cooking', 'question' => 'Was the cooking process easy?', 'value' => $review->ease_of_cooking),
                            array('icon' => '⭐', 'label' => 'Restaurant Style Taste', 'question' => 'Did it taste like restaurant-style food?', 'value' => $review->restaurant_taste),
                            array('icon' => '📋', 'label' => 'Instructions Clarity', 'question' => 'Were the instructions clear?', 'value' => $review->instructions_clarity),
                            array('icon' => '📦', 'label' => 'Packaging Quality', 'question' => 'How was the packaging?', 'value' => $review->packaging_feedback),
                            array('icon' => '💰', 'label' => 'Value for Money', 'question' => 'Is it worth the price?', 'value' => $review->value_for_money),
                            array('icon' => '👍', 'label' => 'Would Recommend', 'question' => 'Would you recommend to others?', 'value' => $review->recommendation),
                            array('icon' => '🔄', 'label' => 'Repeat Purchase', 'question' => 'Would you buy again?', 'value' => $review->repeat_purchase),
                        );

                        $positive_values = array('Excellent', 'Very Easy', 'Yes, absolutely', 'Yes', 'Definitely');
                        $neutral_values = array('Good', 'Easy', 'Somewhat', 'Maybe', 'Average', 'Normal');

                        foreach ($feedback_items as $item):
                            if (empty($item['value'])) continue;

                            if (in_array($item['value'], $positive_values)) {
                                $bg = '#dcfce7'; $text_color = '#16a34a'; $border_color = '#bbf7d0';
                            } elseif (in_array($item['value'], $neutral_values)) {
                                $bg = '#fef9c3'; $text_color = '#a16207'; $border_color = '#fde68a';
                            } else {
                                $bg = '#fee2e2'; $text_color = '#dc2626'; $border_color = '#fecaca';
                            }
                        ?>
                        <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                            <div>
                                <span class="me-2" style="font-size: 1.2rem;"><?php echo $item['icon'] ?></span>
                                <strong><?php echo $item['label'] ?></strong>
                                <br><small class="text-muted ms-4 ps-2"><?php echo $item['question'] ?></small>
                            </div>
                            <span class="px-3 py-1 rounded-pill fw-semibold font-size-13" style="background: <?php echo $bg ?>; color: <?php echo $text_color ?>; border: 1px solid <?php echo $border_color ?>; white-space: nowrap;">
                                <?php echo htmlspecialchars($item['value']) ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Open Text Responses -->
                <div class="row">
                    <?php if (!empty($review->liked_most)): ?>
                    <div class="col-md-6">
                        <div class="card border-start border-3 border-success">
                            <div class="card-body">
                                <h5 class="card-title mb-3">❤️ What They Liked Most</h5>
                                <p class="text-muted mb-0 font-size-15">"<?php echo nl2br(htmlspecialchars($review->liked_most)) ?>"</p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($review->improvement)): ?>
                    <div class="col-md-6">
                        <div class="card border-start border-3 border-warning">
                            <div class="card-body">
                                <h5 class="card-title mb-3">💡 Improvement Suggestions</h5>
                                <p class="text-muted mb-0 font-size-15">"<?php echo nl2br(htmlspecialchars($review->improvement)) ?>"</p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Review Image -->
                <?php if (!empty($review->review_image)): ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">📸 Customer Photo</h5>
                        <img src="<?php echo base_url('uploads/reviews/' . $review->review_image) ?>" class="img-fluid rounded shadow-sm" style="max-height: 400px;" alt="Customer Photo">
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar: Order Info & Products -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4"><i class="uil uil-box text-primary me-2"></i><?php echo $is_guest_review ? 'Guest Info' : 'Order Details' ?></h4>
                        <table class="table table-borderless table-sm mb-0">
                            <?php if (!$is_guest_review): ?>
                            <tr>
                                <td class="text-muted" style="width:40%">Order #</td>
                                <td><a href="<?php echo base_url('admin_root/orders/view/' . $review->order_id) ?>" class="text-primary fw-bold"><?php echo htmlspecialchars($review->order_number) ?></a></td>
                            </tr>
                            <?php else: ?>
                            <tr>
                                <td class="text-muted" style="width:40%">Type</td>
                                <td><span class="badge bg-soft-secondary text-secondary">Guest Review</span></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="text-muted">Customer</td>
                                <td class="fw-semibold"><?php echo !empty($review->customer_name) ? htmlspecialchars($review->customer_name) : (!empty($review->delivery_fullname) ? htmlspecialchars($review->delivery_fullname) : 'Guest') ?></td>
                            </tr>
                            <?php
                                $phone = !empty($review->delivery_phone) ? $review->delivery_phone : (!empty($review->guest_phone) ? $review->guest_phone : '');
                                if (!empty($phone)):
                            ?>
                            <tr>
                                <td class="text-muted">Phone</td>
                                <td><a href="tel:<?php echo $phone ?>"><?php echo htmlspecialchars($phone) ?></a></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!$is_guest_review && !empty($review->total_amount)): ?>
                            <tr>
                                <td class="text-muted">Order Total</td>
                                <td class="fw-semibold">₹<?php echo number_format($review->total_amount, 2) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Order Date</td>
                                <td><?php echo date('d M Y', strtotime($review->order_date)) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="text-muted">Review Date</td>
                                <td><?php echo date('d M Y', strtotime($review->updated_at)) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Products Ordered -->
                <?php if (!empty($order_items)): ?>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3"><i class="uil uil-shopping-bag text-primary me-2"></i>Products Ordered</h4>
                        <?php foreach ($order_items as $item):
                            $img = !empty($item->product_image) ? base_url('uploads/products/' . $item->product_image) : base_url('assets/images/no-image.png');
                        ?>
                        <div class="d-flex align-items-center mb-3 p-2 bg-light rounded">
                            <img src="<?php echo $img ?>" class="avatar-sm rounded-3 me-3 shadow-sm" alt="">
                            <div>
                                <h6 class="mb-0 font-size-14"><?php echo htmlspecialchars($item->product_name) ?></h6>
                                <?php if (!empty($item->variant_name)): ?>
                                <small class="text-muted"><?php echo htmlspecialchars($item->variant_name) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Review Link -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3"><i class="uil uil-link text-primary me-2"></i>Review Link</h4>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" id="review_link_url" value="<?php echo base_url('feedback/' . $review->review_token) ?>" readonly>
                            <button class="btn btn-sm btn-primary" type="button" onclick="copyReviewLink()"><i class="uil-copy me-1"></i>Copy</button>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <a href="<?php echo base_url('admin_root/reviews') ?>" class="btn btn-outline-secondary w-100">
                    <i class="uil uil-arrow-left me-1"></i> Back to Reviews
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyReviewLink() {
    var input = document.getElementById('review_link_url');
    input.select();
    document.execCommand('copy');
    toastr["success"]("Success", "Link copied to clipboard!");
}
</script>
