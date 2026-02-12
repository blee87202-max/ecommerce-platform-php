<?php
/* Smarty version 4.3.4, created on 2026-01-20 11:34:06
  from 'C:\wamp64\www\ecommerce_project\Views\product_quick.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.4',
  'unifunc' => 'content_696f682ee49a01_79386747',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c15e9773e0f271d1dd4ec6e37e082b2dc7c9b909' => 
    array (
      0 => 'C:\\wamp64\\www\\ecommerce_project\\Views\\product_quick.html',
      1 => 1768908744,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_696f682ee49a01_79386747 (Smarty_Internal_Template $_smarty_tpl) {
?><!-- استدعاء ملف CSS المنفصل -->
<link rel="stylesheet" href="../Assets/css/product_quick.css">

<div class="quick-view-container">
    
    <div class="quick-view-main">
        <!-- الصورة على اليمين -->
        <div class="qv-image-section">
            <img src="<?php echo $_smarty_tpl->tpl_vars['product']->value['image_url'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>
" onerror="this.src='../Assets/images/default-product.png'">
        </div>
        
        <!-- التفاصيل على اليسار -->
        <div class="qv-details-section">
            <div class="qv-brand"><?php echo $_smarty_tpl->tpl_vars['product']->value['brand'];?>
</div>
            <h1 class="qv-title"><?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>
</h1>
            
            <div class="qv-rating">
                <span class="rating-count">(<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'number_format' ][ 0 ], array( $_smarty_tpl->tpl_vars['product']->value['review_count'] ));?>
 تقييم)</span>
                <div class="stars">
                    <?php
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable(null, $_smarty_tpl->isRenderingCache);$_smarty_tpl->tpl_vars['i']->step = 1;$_smarty_tpl->tpl_vars['i']->total = (int) ceil(($_smarty_tpl->tpl_vars['i']->step > 0 ? 5+1 - (1) : 1-(5)+1)/abs($_smarty_tpl->tpl_vars['i']->step));
if ($_smarty_tpl->tpl_vars['i']->total > 0) {
for ($_smarty_tpl->tpl_vars['i']->value = 1, $_smarty_tpl->tpl_vars['i']->iteration = 1;$_smarty_tpl->tpl_vars['i']->iteration <= $_smarty_tpl->tpl_vars['i']->total;$_smarty_tpl->tpl_vars['i']->value += $_smarty_tpl->tpl_vars['i']->step, $_smarty_tpl->tpl_vars['i']->iteration++) {
$_smarty_tpl->tpl_vars['i']->first = $_smarty_tpl->tpl_vars['i']->iteration === 1;$_smarty_tpl->tpl_vars['i']->last = $_smarty_tpl->tpl_vars['i']->iteration === $_smarty_tpl->tpl_vars['i']->total;?>
                        <i class="<?php if ($_smarty_tpl->tpl_vars['i']->value <= $_smarty_tpl->tpl_vars['product']->value['avg_rating']) {?>fas<?php } else { ?>far<?php }?> fa-star"></i>
                    <?php }
}
?>
                </div>
            </div>
            
            <div class="qv-price-box">
                <span class="qv-current-price"><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'number_format' ][ 0 ], array( $_smarty_tpl->tpl_vars['product']->value['price'],2 ));?>
 جنيه</span>
                <?php if ($_smarty_tpl->tpl_vars['product']->value['old_price']) {?>
                    <span class="qv-old-price"><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'number_format' ][ 0 ], array( $_smarty_tpl->tpl_vars['product']->value['old_price'],2 ));?>
 جنيه</span>
                <?php }?>
            </div>
            
            <a href="product.php?id=<?php echo $_smarty_tpl->tpl_vars['product']->value['id'];?>
" class="qv-btn-details">
                اطلع على تفاصيل المنتج
            </a>
        </div>
    </div>
    
    <!-- المنتجات المشابهة في الأسفل -->
    <?php if ($_smarty_tpl->tpl_vars['related_products']->value) {?>
    <div class="qv-similar-section">
        <div class="qv-similar-title">اشترى العملاء أيضًا</div>
        <div class="qv-similar-grid">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['related_products']->value, 'r');
$_smarty_tpl->tpl_vars['r']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['r']->value) {
$_smarty_tpl->tpl_vars['r']->do_else = false;
?>
                <a href="javascript:void(0)" 
                   onclick="if(window.HomeApp && window.HomeApp.openQuickView) { window.HomeApp.openQuickView(<?php echo $_smarty_tpl->tpl_vars['r']->value['id'];?>
); } else { window.location.href='product.php?id=<?php echo $_smarty_tpl->tpl_vars['r']->value['id'];?>
'; }" 
                   class="qv-similar-item" 
                   title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['r']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
">
                    <img src="image.php?src=<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'urlencode' ][ 0 ], array( $_smarty_tpl->tpl_vars['r']->value['image'] ));?>
&w=150&h=150&q=80" 
                         alt="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['r']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
"
                         onerror="this.src='../Assets/images/default-product.png'">
                </a>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div>
    </div>
    <?php }?>
</div><?php }
}
