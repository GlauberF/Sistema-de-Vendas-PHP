<?php $this->load->view("partial/header"); ?>


<div id="register_wrapper">
</div>



<div class="row">
	<div class="col-xs-6">
	<div class="content-form">
		<?php echo form_open("sales/change_mode",array('id'=>'mode_form')); ?>	<span><?php echo $this->lang->line('sales_mode') ?></span>	<?php echo form_dropdown('mode',$modes,$mode,'onchange="$(\'#mode_form\').submit();"'); ?></form>
		<?php echo form_open("sales/add",array('id'=>'add_item_form')); ?><label id="item_label" for="item">	Nombre del Producto	</label><?php echo form_input(array('name'=>'item','id'=>'item','size'=>'40','class'=>'input-xxlarge'));?></form>
	</div>
	</div>
	<div class="col-xs-6">
		<div class="content-btn">
			<?php 
			echo anchor("sales/suspended/width:425","<div class='btn btn-primary'><span style='font-size:73%;'>".$this->lang->line('sales_suspended_sales')."</span></div>",array('class'=>'thickbox none','title'=>$this->lang->line('sales_suspended_sales')));
			echo anchor("customers/view/-1/width:350","<div class='btn btn-primary'><span>".$this->lang->line('sales_new_customer')."</span></div>",array('class'=>'thickbox none','title'=>$this->lang->line('sales_new_customer')));
			echo anchor("items/view/-1/width:360","<div class='btn btn-primary'><span>".$this->lang->line('sales_new_item')."</span></div>",array('class'=>'thickbox none','title'=>$this->lang->line('sales_new_item')));
			?>
		</div>
	</div>
</div>




<div class="row">
	<div class="col-xs-12 col-sm-6 col-lg-8">
		<table class="table table-bordered">
			<tbody>
				<tr class="well">
					<td><?php echo $this->lang->line('common_delete'); ?></td>
					<td><?php echo $this->lang->line('sales_item_name'); ?></td>
					<td><?php echo $this->lang->line('sales_price'); ?></td>
					<td><?php echo $this->lang->line('sales_quantity'); ?></td>
					<td><?php echo $this->lang->line('sales_total'); ?></td>
					<td><?php echo $this->lang->line('sales_edit'); ?></td>
				</tr>
				<?php
				if(count($cart)==0)
				{
					?>
					<tr><td colspan='8'>
						<div class='warning_message' style='padding:7px;'><?php echo $this->lang->line('sales_no_items_in_cart'); ?></div>
					</tr></tr>
					<?php
				}
				else
				{
					foreach(array_reverse($cart, true) as $line=>$item)
					{
						$cur_item_info = $this->Item->get_info($item['item_id']);
						echo form_open("sales/edit_item/$line");
						?>
						<tr>
							<td><?php echo anchor("sales/delete_item/$line",'['.$this->lang->line('common_delete').']');?></td>
							<td ><?php echo $item['name']; ?><br /> [<?php echo $cur_item_info->quantity; ?> in stock]</td>
							<?php if ($items_module_allowed)
							{
								?>
								<td><?php echo form_input(array('name'=>'price','value'=>$item['price'],'size'=>'6'));?></td>
								<?php
							}
							else
							{
								?>
								<td><?php echo $item['price']; ?></td>
								<?php echo form_hidden('price',$item['price']); ?>
								<?php
							}
							?>
							<td>
								<?php
								if($item['is_serialized']==1)
								{
									echo $item['quantity'];
									echo form_hidden('quantity',$item['quantity']);
								}
								else
								{
									echo form_input(array('name'=>'quantity','value'=>$item['quantity'],'size'=>'2'));
								}
								?>
							</td>
							<td><?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100); ?></td>
							<td><?php echo form_submit("edit_item", $this->lang->line('sales_edit_item'));?></td>
						</tr>
					</form>
					<?php
				}
			}
			?> 
		</tbody>
	</table>

</div>
<div class="col-xs-6 col-lg-4">
	<table class="table table-bordered">
		<tbody>
			<tr>
				<td>
					<div id="overall_sale">
						<?php
						if(isset($customer))
						{
							echo $this->lang->line("sales_customer").': <b>'.$customer. '</b><br />';
							echo anchor("sales/remove_customer",'['.$this->lang->line('common_remove').' '.$this->lang->line('customers_customer').']');
						}
						else
						{
							echo form_open("sales/select_customer",array('id'=>'select_customer_form')); ?>
							<label id="customer_label" for="customer"><?php echo $this->lang->line('sales_select_customer'); ?></label>
							<?php echo form_input(array('name'=>'customer','id'=>'customer','size'=>'30','value'=>$this->lang->line('sales_start_typing_customer_name')));?>
						</form>

						<?php
					}
					?>
					<div id='sale_details'>
						<div class="float_left" style="width:55%;"><?php echo $this->lang->line('sales_sub_total'); ?>:</div>
						<div class="float_left" style="width:45%;font-weight:bold;"><?php echo to_currency($subtotal); ?></div>
						<?php foreach($taxes as $name=>$value) { ?>
						<div class="float_left" style='width:55%;'><?php echo $name; ?>:</div>
						<div class="float_left" style="width:45%;font-weight:bold;"><?php echo to_currency($value); ?></div>
						<?php }; ?>
						<div class="float_left" style='width:55%;'><?php echo $this->lang->line('sales_total'); ?>:</div>
						<div class="float_left" style="width:45%;font-weight:bold;"><?php echo to_currency($total); ?></div>
					</div>
					<?php if(count($cart) > 0){ ?>
					<div id="Cancel_sale">
						<?php echo form_open("sales/cancel_sale",array('id'=>'cancel_sale_form')); ?><div class='btn btn-primary' id='cancel_sale_button' style='margin-top:5px;'><span><?php echo $this->lang->line('sales_cancel_sale'); ?></span></div></form>
					</div>

					<div class="clearfix" style="margin-bottom:1px;">&nbsp;</div>
					<?php if(count($payments) > 0){ ?>
					<div id="finish_sale">
						<?php echo form_open("sales/complete",array('id'=>'finish_sale_form')); ?>
						<label id="comment_label" for="comment"><?php echo $this->lang->line('common_comments'); ?>:</label>
						<?php echo form_textarea(array('name'=>'comment', 'id' => 'comment', 'value'=>$comment,'rows'=>'4','cols'=>'23'));?>
						<br /><br />

						<?php

						if(!empty($customer_email))
						{
							echo $this->lang->line('sales_email_receipt'). ': '. form_checkbox(array(
								'name'        => 'email_receipt',
								'id'          => 'email_receipt',
								'value'       => '1',
								'checked'     => (boolean)$email_receipt,
								)).'<br />('.$customer_email.')<br />';
						}

						if ($payments_cover_total)
						{
							echo "<div class='btn btn-primary' id='finish_sale_button' style='float:left;margin-top:5px;'><span>".$this->lang->line('sales_complete_sale')."</span></div>";
						}
						echo "<div class='btn btn-primary' id='suspend_sale_button' style='float:right;margin-top:5px;'><span>".$this->lang->line('sales_suspend_sale')."</span></div>";
						?>
					</div>
				</form>
				<?php
			}
			?>

			<table width="100%">
				<tr>
					<td style="width:55%; "><div class="float_left"><?php echo $this->lang->line('sales_payments_total').':';?></div></td>
					<td style="width:45%; text-align:right;"><div class="float_left" style="text-align:right;font-weight:bold;"><?php echo to_currency($payments_total); ?></div></td>
				</tr>
				<tr>
					<td style="width:55%; "><div class="float_left" ><?php echo $this->lang->line('sales_amount_due').':';?></div></td>
					<td style="width:45%; text-align:right; "><div class="float_left" style="text-align:right;font-weight:bold;"><?php echo to_currency($amount_due); ?></div></td>
				</tr>
			</table>

			<div id="Payment_Types" >
				<?php echo form_open("sales/add_payment",array('id'=>'add_payment_form')); ?>
				<table width="100%">
					<tr>
						<td><?php echo $this->lang->line('sales_payment').':   ';?>	</td>					
						<td><?php echo form_dropdown( 'payment_type', $payment_options, array(), 'id="payment_types"' ); ?>	</td>
					</tr>
					<tr>
						<td><span id="amount_tendered_label"><?php echo $this->lang->line( 'sales_amount_tendered' ).': '; ?></span></td>
						<td><?php echo form_input( array( 'name'=>'amount_tendered', 'id'=>'amount_tendered', 'value'=>to_currency_no_money($amount_due), 'size'=>'10' ) );?></td>
					</tr>
				</table>
				<div class='btn btn-primary' id='add_payment_button' style='float:left;margin-top:5px;'>
					<span><?php echo $this->lang->line('sales_add_payment'); ?></span>
				</div>
			</form>
			<?php
			if(count($payments) > 0)
			{
				?>
				<table id="register">
					<thead>
						<tr>
							<th style="width:11%;"><?php echo $this->lang->line('common_delete'); ?></th>
							<th style="width:60%;"><?php echo $this->lang->line('sales_payment_type'); ?></th>
							<th style="width:18%;"><?php echo $this->lang->line('sales_payment_amount'); ?></th>
						</tr>
					</thead>
					<tbody id="payment_contents">
						<?php
						foreach($payments as $payment_id=>$payment)
						{
							echo form_open("sales/edit_payment/$payment_id",array('id'=>'edit_payment_form'.$payment_id));
							?>
							<tr>
								<td><?php echo anchor( "sales/delete_payment/$payment_id", '['.$this->lang->line('common_delete').']' ); ?></td>
								<td><?php echo $payment['payment_type']; ?></td>
								<td style="text-align:right;"><?php echo to_currency( $payment['payment_amount'] ); ?></td>
							</tr>
						</form>
						<?php
					}
					?>
				</tbody>
			</table>
			<br />
			<?php } ?>
		</div>

		<?php
	}
	?>
</div>
</td>
</tr>
</tbody>
</table>
</div>
</div>




<?php
if(isset($error))
{
	echo "<div class='error_message'>".$error."</div>";
}
if (isset($warning))
{
	echo "<div class='warning_mesage'>".$warning."</div>";
}

if (isset($success))
{
	echo "<div class='success_message'>".$success."</div>";
}
?>





<div class="clearfix" style="margin-bottom:30px;">&nbsp;</div>


<?php $this->load->view("partial/footer"); ?>

<script type="text/javascript" language="javascript">
	$(document).ready(function()
	{
		$("#item").autocomplete('<?php echo site_url("sales/item_search"); ?>',
		{
			minChars:0,
			max:100,
			selectFirst: false,
			delay:10,
			formatItem: function(row) {
				return row[1];
			}
		});

		$("#item").result(function(event, data, formatted)
		{
			$("#add_item_form").submit();
		});

		$('#item').focus();

		$('#item').blur(function()
		{
			$(this).attr('value',"<?php echo $this->lang->line('sales_start_typing_item_name'); ?>");
		});

		$('#item,#customer').click(function()
		{
			$(this).attr('value','');
		});

		$("#customer").autocomplete('<?php echo site_url("sales/customer_search"); ?>',
		{
			minChars:0,
			delay:10,
			max:100,
			formatItem: function(row) {
				return row[1];
			}
		});

		$("#customer").result(function(event, data, formatted)
		{
			$("#select_customer_form").submit();
		});

		$('#customer').blur(function()
		{
			$(this).attr('value',"<?php echo $this->lang->line('sales_start_typing_customer_name'); ?>");
		});

		$('#comment').change(function() 
		{
			$.post('<?php echo site_url("sales/set_comment");?>', {comment: $('#comment').val()});
		});

		$('#email_receipt').change(function() 
		{
			$.post('<?php echo site_url("sales/set_email_receipt");?>', {email_receipt: $('#email_receipt').is(':checked') ? '1' : '0'});
		});


		$("#finish_sale_button").click(function()
		{
			if (confirm('<?php echo $this->lang->line("sales_confirm_finish_sale"); ?>'))
			{
				$('#finish_sale_form').submit();
			}
		});

		$("#suspend_sale_button").click(function()
		{
			if (confirm('<?php echo $this->lang->line("sales_confirm_suspend_sale"); ?>'))
			{
				$('#finish_sale_form').attr('action', '<?php echo site_url("sales/suspend"); ?>');
				$('#finish_sale_form').submit();
			}
		});

		$("#cancel_sale_button").click(function()
		{
			if (confirm('<?php echo $this->lang->line("sales_confirm_cancel_sale"); ?>'))
			{
				$('#cancel_sale_form').submit();
			}
		});

		$("#add_payment_button").click(function()
		{
			$('#add_payment_form').submit();
		});

		$("#payment_types").change(checkPaymentTypeGiftcard).ready(checkPaymentTypeGiftcard)
	});

function post_item_form_submit(response)
{
	if(response.success)
	{
		$("#item").attr("value",response.item_id);
		$("#add_item_form").submit();
	}
}

function post_person_form_submit(response)
{
	if(response.success)
	{
		$("#customer").attr("value",response.person_id);
		$("#select_customer_form").submit();
	}
}

function checkPaymentTypeGiftcard()
{
	if ($("#payment_types").val() == "<?php echo $this->lang->line('sales_giftcard'); ?>")
	{
		$("#amount_tendered_label").html("<?php echo $this->lang->line('sales_giftcard_number'); ?>");
		$("#amount_tendered").val('');
		$("#amount_tendered").focus();
	}
	else
	{
		$("#amount_tendered_label").html("<?php echo $this->lang->line('sales_amount_tendered'); ?>");
	}
}

</script>
