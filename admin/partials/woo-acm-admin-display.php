<?php

/**
* Provide a admin area view for the plugin
*
* This file is used to markup the admin-facing aspects of the plugin.
*
* @link       https://github.com/meet-tech-expert
* @since      1.0.0
*
* @package    Woo_Acm
* @subpackage Woo_Acm/admin/partials
*/

$settings =  get_option('wc_acm_settings');
$data = array();
if($settings){
	$data = maybe_unserialize($settings);	
}
$enable =  get_option('wc_enable_acm');
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<table class="form-table">

	<tbody>
		<tr valign="top" class="">
			<th scope="row" class="titledesc"><?php _e('Enable/Disable',$this->plugin_name); ?></th>
			<td class="forminp forminp-checkbox">
				<fieldset>
					<legend class="screen-reader-text"><span><?php _e('Enable/Disable',$this->plugin_name); ?></span></legend>
					<label for="wc_settings_tab_enable_acm">
						<input name="wc_enable_acm" id="wc_enable_acm" type="checkbox" class="" value="1" <?php echo ($enable && $enable=='1')?'checked':''; ?>> 							
					</label> 									
				</fieldset>
			</td> 
		</tr>
		<?php if(empty($data)){ ?>
		<tr valign="top" id="firstRow">
			<th scope="row" class="titledesc">
				<button type="button" class="button" id="add_more"><?php _e('Add More',$this->plugin_name); ?></button>
			</th>
			<td class="forminp forminp-number">
				<input name="wc_acm_sales_amount[]" id="" type="number" style="width:150px;" value="" class="" placeholder="<?php _e('Sales Amount',$this->plugin_name); ?>" min="1" required="required"> 
						
			<input name="wc_acm_coupon_amount[]" id="" type="number" style="width:150px;" value="" class="" placeholder="<?php _e('Coupon Amount',$this->plugin_name); ?>" min="1" required="required">
			</td>
		</tr>
		<?php }else{
		foreach($data as $key => $row){
		?>
		<tr valign="top" id="<?php echo ($key=='0')?'firstRow':''; ?>" class="<?php echo ($key!='0')?'more_rows':''; ?>">
			<th scope="row" class="titledesc">
			  <?php if($key=='0'){ ?>
				<button type="button" class="button" id="add_more"><?php _e('Add More',$this->plugin_name); ?></button>
				<?php } ?>
			</th>
			<td class="forminp forminp-number">
				<input name="wc_acm_sales_amount[]" id="" type="number" style="width:150px;" value="<?php echo $row['sales']; ?>" class="" placeholder="<?php _e('Sales Amount',$this->plugin_name); ?>" min="1" required="required"> 
						
			<input name="wc_acm_coupon_amount[]" id="" type="number" style="width:150px;" value="<?php echo $row['coupon']; ?>" class="" placeholder="<?php _e('Coupon Amount',$this->plugin_name); ?>" min="1" required="required">
			<?php if($key!='0'){ ?>
			<button class="button remove_rows" type="button" ><?php _e('Remove',$this->plugin_name); ?></button>
			<?php } ?>
			</td>
		</tr>
		<?php } } ?>

	</tbody>
</table>									
<table class="form-table">
	<tbody id="cloneDiv">
	</tbody>	
</table>
