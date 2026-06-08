<?php
$collection_presets = array(
    array('label' => 'ABCD Stickers', 'icon' => 'fa-font', 'gradient' => 'category-gradient-pink'),
    array('label' => 'Animal World', 'icon' => 'fa-paw', 'gradient' => 'category-gradient-green'),
    array('label' => 'Dino Land', 'icon' => 'fa-dragon', 'gradient' => 'category-gradient-yellow'),
    array('label' => 'Aqua Fun', 'icon' => 'fa-fish', 'gradient' => 'category-gradient-blue'),
    array('label' => 'Space Adventure', 'icon' => 'fa-rocket', 'gradient' => 'category-gradient-purple'),
    array('label' => 'Cartoon Stickers', 'icon' => 'fa-face-smile', 'gradient' => 'category-gradient-orange'),
    array('label' => 'Stationery', 'icon' => 'fa-pencil', 'gradient' => 'category-gradient-pink'),
    array('label' => 'Gift Combos', 'icon' => 'fa-gift', 'gradient' => 'category-gradient-purple'),
);

$display_items = array();
if (!empty($categories) && count($categories) > 0) {
    foreach ($categories as $index => $category) {
        $preset = $collection_presets[$index % count($collection_presets)];
        $display_items[] = array(
            'name' => $category->category_name,
            'url' => category_url($category),
            'icon_file' => !empty($category->icon) ? $category->icon : null,
            'preset' => $preset,
        );
    }
    while (count($display_items) < 8) {
        $i = count($display_items);
        $preset = $collection_presets[$i % count($collection_presets)];
        $display_items[] = array(
            'name' => $preset['label'],
            'url' => base_url('shop/search?q=' . urlencode($preset['label'])),
            'icon_file' => null,
            'preset' => $preset,
        );
    }
} else {
    foreach ($collection_presets as $preset) {
        $display_items[] = array(
            'name' => $preset['label'],
            'url' => base_url('shop/search?q=' . urlencode($preset['label'])),
            'icon_file' => null,
            'preset' => $preset,
        );
    }
}
?>
<section class="home-section bg-white border-t border-gray-100">
    <div class="page-container">
        <div class="section-header">
            <h2 class="home-section-title">Shop by Category</h2>
            <p class="section-subtitle">Browse our most popular collections</p>
        </div>

        <div class="category-grid">
            <?php foreach (array_slice($display_items, 0, 8) as $item): ?>
                <?php $preset = $item['preset']; ?>
                <a href="<?php echo $item['url']; ?>" class="category-tile group">
                    <div class="category-tile-visual <?php echo $preset['gradient']; ?>">
                        <?php if (!empty($item['icon_file'])): ?>
                            <img src="<?php echo base_url('uploads/categories/' . $item['icon_file']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-full object-cover" loading="lazy">
                        <?php else: ?>
                            <div class="category-tile-fallback">
                                <i class="fa-solid <?php echo $preset['icon']; ?>"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <span class="category-tile-label"><?php echo htmlspecialchars($item['name']); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
