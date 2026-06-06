<?php
    $dur = intval($session->duration_seconds);
    $duration_str = $dur > 0 ? ($dur >= 60 ? floor($dur / 60) . 'm ' . ($dur % 60) . 's' : $dur . 's') : '0s';

    $device_icon = 'uil-monitor';
    if ($session->device_type == 'mobile') $device_icon = 'uil-mobile-android';
    elseif ($session->device_type == 'tablet') $device_icon = 'uil-tablet';

    $is_new = ($session->visitor_first_seen >= $session->started_at);
?>
<div class="page-content">
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Session Journey</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('admin_root/analytics') ?>">Analytics</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo base_url('admin_root/analytics/sessions') ?>">Sessions</a></li>
                            <li class="breadcrumb-item active">Journey #<?php echo $session->id ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Session Info Sidebar -->
            <div class="col-xl-4">
                <!-- Visitor Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><i class="uil uil-user-circle text-primary me-2"></i>Visitor</h5>
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted" style="width:40%">Name</td>
                                <td class="fw-semibold"><?php echo !empty($session->user_name) ? htmlspecialchars($session->user_name) : '<span class="text-muted">Guest</span>' ?></td>
                            </tr>
                            <?php if (!empty($session->user_phone)): ?>
                            <tr>
                                <td class="text-muted">Phone</td>
                                <td><a href="tel:<?php echo $session->user_phone ?>"><?php echo htmlspecialchars($session->user_phone) ?></a></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($session->user_email)): ?>
                            <tr>
                                <td class="text-muted">Email</td>
                                <td><?php echo htmlspecialchars($session->user_email) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="text-muted">Type</td>
                                <td>
                                    <?php if ($is_new): ?>
                                        <span class="badge bg-soft-success text-success">New Visitor</span>
                                    <?php else: ?>
                                        <span class="badge bg-soft-primary text-primary">Returning</span>
                                        <small class="text-muted ms-1">(<?php echo intval($session->visitor_total_sessions) ?> total sessions)</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">First Seen</td>
                                <td><?php echo date('d M Y', strtotime($session->visitor_first_seen)) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Session Details Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><i class="uil uil-browser text-info me-2"></i>Session Details</h5>
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted" style="width:40%">Started</td>
                                <td><?php echo date('d M Y, h:i:s A', strtotime($session->started_at)) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Duration</td>
                                <td><strong><?php echo $duration_str ?></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pages Viewed</td>
                                <td><strong><?php echo intval($session->page_views) ?></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Device</td>
                                <td><i class="<?php echo $device_icon ?> me-1"></i><?php echo ucfirst($session->device_type) ?></td>
                            </tr>
                            <?php if (!empty($session->browser)): ?>
                            <tr>
                                <td class="text-muted">Browser</td>
                                <td><?php echo htmlspecialchars($session->browser) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($session->os)): ?>
                            <tr>
                                <td class="text-muted">OS</td>
                                <td><?php echo htmlspecialchars($session->os) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($session->screen_resolution)): ?>
                            <tr>
                                <td class="text-muted">Screen</td>
                                <td><?php echo htmlspecialchars($session->screen_resolution) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($session->ip_address)): ?>
                            <tr>
                                <td class="text-muted">IP</td>
                                <td><code><?php echo htmlspecialchars($session->ip_address) ?></code></td>
                            </tr>
                            <?php endif; ?>
                            <?php
                                $location = '';
                                if (!empty($session->city)) $location .= $session->city;
                                if (!empty($session->region)) $location .= ($location ? ', ' : '') . $session->region;
                                if (!empty($session->country)) $location .= ($location ? ', ' : '') . $session->country;
                            ?>
                            <?php if (!empty($location)): ?>
                            <tr>
                                <td class="text-muted">Location</td>
                                <td><i class="uil uil-map-marker me-1"></i><?php echo htmlspecialchars($location) ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>

                <!-- Traffic Source Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><i class="uil uil-link-alt text-warning me-2"></i>Traffic Source</h5>
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted" style="width:40%">Referrer</td>
                                <td><?php echo !empty($session->referrer) ? '<a href="' . htmlspecialchars($session->referrer) . '" target="_blank" class="text-primary">' . htmlspecialchars(parse_url($session->referrer, PHP_URL_HOST)) . '</a>' : '<span class="text-muted">Direct</span>' ?></td>
                            </tr>
                            <?php if (!empty($session->utm_source)): ?>
                            <tr>
                                <td class="text-muted">UTM Source</td>
                                <td><span class="badge bg-soft-info text-info"><?php echo htmlspecialchars($session->utm_source) ?></span></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($session->utm_medium)): ?>
                            <tr>
                                <td class="text-muted">UTM Medium</td>
                                <td><?php echo htmlspecialchars($session->utm_medium) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($session->utm_campaign)): ?>
                            <tr>
                                <td class="text-muted">UTM Campaign</td>
                                <td><?php echo htmlspecialchars($session->utm_campaign) ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>

                <!-- Conversion Status -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><i class="uil uil-check-circle text-success me-2"></i>Conversion Status</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge px-3 py-2 <?php echo $session->is_bounce ? 'bg-soft-warning text-warning' : 'bg-soft-success text-success' ?> font-size-13">
                                <i class="uil <?php echo $session->is_bounce ? 'uil-arrow-down-right' : 'uil-check' ?> me-1"></i>
                                <?php echo $session->is_bounce ? 'Bounced' : 'Engaged' ?>
                            </span>
                            <span class="badge px-3 py-2 <?php echo $session->has_cart_activity ? 'bg-soft-info text-info' : 'bg-soft-secondary text-secondary' ?> font-size-13">
                                <i class="uil uil-shopping-cart me-1"></i>
                                <?php echo $session->has_cart_activity ? 'Added to Cart' : 'No Cart Activity' ?>
                            </span>
                            <span class="badge px-3 py-2 <?php echo $session->has_checkout ? 'bg-soft-primary text-primary' : 'bg-soft-secondary text-secondary' ?> font-size-13">
                                <i class="uil uil-credit-card me-1"></i>
                                <?php echo $session->has_checkout ? 'Checkout Started' : 'No Checkout' ?>
                            </span>
                            <span class="badge px-3 py-2 <?php echo $session->has_purchase ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' ?> font-size-13">
                                <i class="uil uil-check-circle me-1"></i>
                                <?php echo $session->has_purchase ? 'Purchased' : 'No Purchase' ?>
                            </span>
                        </div>
                        <?php if ($session->has_purchase && !empty($session->order_id)): ?>
                        <div class="mt-3">
                            <a href="<?php echo base_url('admin_root/orders/view/' . $session->order_id) ?>" class="btn btn-sm btn-soft-success">
                                <i class="uil uil-external-link-alt me-1"></i>View Order #<?php echo $session->order_id ?>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Back Button -->
                <a href="<?php echo base_url('admin_root/analytics/sessions') ?>" class="btn btn-outline-secondary w-100 mb-4">
                    <i class="uil uil-arrow-left me-1"></i> Back to Sessions
                </a>
            </div>

            <!-- Journey Timeline -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="uil uil-route text-primary me-2"></i>Session Journey
                            <span class="badge bg-soft-primary text-primary ms-2"><?php echo count($timeline) ?> steps</span>
                        </h5>

                        <?php if (!empty($timeline)): ?>
                        <div class="journey-timeline">
                            <?php
                            $step = 0;
                            $prev_time = null;
                            foreach ($timeline as $item):
                                $step++;
                                $is_event = ($item['type'] === 'event');
                                $d = $item['data'];
                                $time = date('h:i:s A', strtotime($item['timestamp']));

                                // Time gap from previous step
                                $gap_str = '';
                                if ($prev_time !== null) {
                                    $gap = strtotime($item['timestamp']) - strtotime($prev_time);
                                    if ($gap > 0) {
                                        if ($gap >= 60) $gap_str = floor($gap / 60) . 'm ' . ($gap % 60) . 's later';
                                        else $gap_str = $gap . 's later';
                                    }
                                }
                                $prev_time = $item['timestamp'];

                                if ($is_event):
                                    // Event styling
                                    $event_icons = array(
                                        'cart_add' => array('icon' => 'uil-shopping-cart-alt', 'color' => '#4ba6ef', 'bg' => '#e0f2fe', 'label' => 'Added to Cart'),
                                        'cart_remove' => array('icon' => 'uil-trash-alt', 'color' => '#fd625e', 'bg' => '#fee2e2', 'label' => 'Removed from Cart'),
                                        'cart_update' => array('icon' => 'uil-sync', 'color' => '#ffbf53', 'bg' => '#fef3c7', 'label' => 'Updated Cart'),
                                        'checkout_start' => array('icon' => 'uil-credit-card', 'color' => '#5156be', 'bg' => '#ede9fe', 'label' => 'Started Checkout'),
                                        'purchase' => array('icon' => 'uil-check-circle', 'color' => '#2ab57d', 'bg' => '#dcfce7', 'label' => 'Completed Purchase'),
                                        'search' => array('icon' => 'uil-search', 'color' => '#ffbf53', 'bg' => '#fef3c7', 'label' => 'Searched'),
                                        'wishlist_add' => array('icon' => 'uil-heart', 'color' => '#fd625e', 'bg' => '#fee2e2', 'label' => 'Added to Wishlist'),
                                    );
                                    $ev_config = isset($event_icons[$d->event_type]) ? $event_icons[$d->event_type] : array('icon' => 'uil-bolt', 'color' => '#95a5a6', 'bg' => '#f3f4f6', 'label' => $d->event_type);
                            ?>
                            <?php if (!empty($gap_str)): ?>
                            <div class="timeline-gap">
                                <small class="text-muted"><i class="uil uil-clock-three me-1"></i><?php echo $gap_str ?></small>
                            </div>
                            <?php endif; ?>
                            <div class="timeline-item timeline-event">
                                <div class="timeline-marker" style="background: <?php echo $ev_config['bg'] ?>; border-color: <?php echo $ev_config['color'] ?>;">
                                    <i class="<?php echo $ev_config['icon'] ?>" style="color: <?php echo $ev_config['color'] ?>;"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong style="color: <?php echo $ev_config['color'] ?>;"><?php echo $ev_config['label'] ?></strong>
                                            <?php if (!empty($d->event_label)): ?>
                                                <span class="text-muted ms-2"><?php echo htmlspecialchars($d->event_label) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($d->event_value)): ?>
                                                <span class="badge bg-soft-success text-success ms-1">&#8377;<?php echo number_format($d->event_value, 2) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($d->order_id)): ?>
                                                <a href="<?php echo base_url('admin_root/orders/view/' . $d->order_id) ?>" class="ms-1 text-primary fw-bold">Order #<?php echo $d->order_id ?></a>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted text-nowrap ms-2"><?php echo $time ?></small>
                                    </div>
                                </div>
                            </div>

                            <?php else:
                                    // Pageview styling
                                    $page_url = !empty($d->page_url) ? preg_replace('/^https?:\/\/[^\/]+/', '', $d->page_url) : '/';
                                    $page_type = !empty($d->page_type) ? $d->page_type : 'other';

                                    $type_colors = array(
                                        'home' => '#5156be', 'product' => '#2ab57d', 'category' => '#4ba6ef',
                                        'cart' => '#ffbf53', 'checkout' => '#fd625e', 'account' => '#5156be',
                                        'search' => '#ffbf53', 'login' => '#95a5a6', 'page' => '#95a5a6',
                                    );
                                    $type_color = isset($type_colors[$page_type]) ? $type_colors[$page_type] : '#95a5a6';

                                    $time_on_page = '';
                                    if (!empty($d->time_on_page) && $d->time_on_page > 0) {
                                        $top = intval($d->time_on_page);
                                        $time_on_page = $top >= 60 ? floor($top / 60) . 'm ' . ($top % 60) . 's' : $top . 's';
                                    }

                                    $is_entry = ($step === 1);
                                    $is_exit = ($step === count($timeline) && !$is_event);
                            ?>
                            <?php if (!empty($gap_str)): ?>
                            <div class="timeline-gap">
                                <small class="text-muted"><i class="uil uil-clock-three me-1"></i><?php echo $gap_str ?></small>
                            </div>
                            <?php endif; ?>
                            <div class="timeline-item timeline-pageview <?php echo $is_entry ? 'timeline-entry' : '' ?> <?php echo $is_exit ? 'timeline-exit' : '' ?>">
                                <div class="timeline-marker" style="background: <?php echo $type_color ?>15; border-color: <?php echo $type_color ?>;">
                                    <span class="timeline-step" style="color: <?php echo $type_color ?>;"><?php echo $step ?></span>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <?php if ($is_entry): ?>
                                                <span class="badge bg-success me-1">Entry</span>
                                            <?php endif; ?>
                                            <?php if ($is_exit): ?>
                                                <span class="badge bg-danger me-1">Exit</span>
                                            <?php endif; ?>
                                            <span class="badge me-1" style="background: <?php echo $type_color ?>20; color: <?php echo $type_color ?>;"><?php echo $page_type ?></span>
                                            <code class="text-dark"><?php echo htmlspecialchars($page_url) ?></code>
                                            <?php if (!empty($time_on_page)): ?>
                                                <small class="text-muted ms-2"><i class="uil uil-stopwatch me-1"></i><?php echo $time_on_page ?></small>
                                            <?php endif; ?>
                                            <?php if (!empty($d->search_query)): ?>
                                                <small class="text-warning ms-2"><i class="uil uil-search me-1"></i>"<?php echo htmlspecialchars($d->search_query) ?>"</small>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted text-nowrap ms-2"><?php echo $time ?></small>
                                    </div>
                                </div>
                            </div>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="uil uil-route" style="font-size: 48px; opacity: 0.3;"></i>
                            <p class="mt-2">No journey data recorded for this session</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
/* Journey Timeline */
.journey-timeline {
    position: relative;
    padding-left: 45px;
}
.journey-timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
}
.timeline-item {
    position: relative;
    margin-bottom: 0;
    padding: 10px 0 10px 20px;
}
.timeline-marker {
    position: absolute;
    left: -45px;
    top: 10px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    z-index: 1;
}
.timeline-marker i {
    font-size: 16px;
}
.timeline-step {
    font-weight: 700;
    font-size: 14px;
}
.timeline-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 10px 14px;
    border: 1px solid #e9ecef;
}
.timeline-event .timeline-content {
    background: #fff;
    border-left: 3px solid;
}
.timeline-entry .timeline-content {
    border: 1px solid #bbf7d0;
    background: #f0fdf4;
}
.timeline-exit .timeline-content {
    border: 1px solid #fecaca;
    background: #fef2f2;
}
.timeline-gap {
    padding: 4px 0 4px 20px;
    position: relative;
}
.timeline-gap::before {
    content: '|';
    position: absolute;
    left: -30px;
    color: #d1d5db;
    font-size: 12px;
}
</style>
