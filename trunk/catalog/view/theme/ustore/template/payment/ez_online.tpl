<?php if ($testmode) { ?>
<div class="warning"><?php echo $text_testmode; ?></div>
<?php } ?>
<form action="<?php echo $action; ?>" method="post">
  <input type="hidden" name="merchantInvoice" value="<?php echo $invoice; ?>" />
  <input type="hidden" name="custom" value="<?php echo $custom; ?>" />
  <input type="hidden" name="bn" value="OpenCart_Cart_WPS" />  
  <div class="buttons">
    <div class="right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="button" />
    </div>
  </div>
</form>
