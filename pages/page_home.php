<?php
wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false );
wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false );

//if( $_GET['test'] == 1 ) echo "<pre>".print_r( $this, true )."</pre>";

$pagehook = $this->m_pages['home']['pagehook'];
?>

<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.js" type="text/javascript"></script>-->
<script>
jQuery(document).ready(function()
{
	jQuery('.plugin_install').click( function()
	{
		var nextItem = jQuery(this);
		var nextParent = nextItem.parent().parent();
		var rowParent = nextParent.parent();
		
	  	jQuery.ajax(
		{
			type: "POST",
			url: "<?= $this->GetUrl(); ?>/ajax/install_plugin.php",
			dataType: 'json',
			data: "plugin_url=" + nextItem.attr( 'rel' ),
			beforeSend: function()
			{
				nextParent.html( '<img src="<?= $this->GetUrl(); ?>/images/loading3.gif" />' );
			},
			success: function(msg)
			{
				var result = jQuery.parseJSON( msg );
				//nextItem.append( msg );
				
				nextParent.html( '<span class="installed">installed</span>' );
				
				nextParent.parent('tr').find('.plugin_activate').parent().css( 'display', 'block' );
			}
		});
	});	
	
	jQuery('.plugin_activate').click( function()
	{
		var nextItem = jQuery(this);
		var nextParent = nextItem.parent().parent();
		
	  	jQuery.ajax(
		{
			type: "POST",
			url: "<?= $this->GetUrl(); ?>/ajax/activate_plugin.php",
			
			data: "plugin_url=" + nextItem.attr( 'rel' ),
			beforeSend: function()
			{
				nextParent.html( '<img src="<?= $this->GetUrl(); ?>/images/loading3.gif" />' );
			},
			success: function(msg)
			{
				var result = jQuery.parseJSON( msg );
				//nextItem.append( msg );
				
				nextParent.html( '<span class="activated">activated</span>' );
			}
		});
	});	
});
</script>
<div class="wrap">
	
	<h2>Qody's Owl Emporium</h2>

	<?php //$this->GetClass('postman')->DisplayMessages(); ?>

	<div id="poststuff" class="metabox-holder has-right-sidebar">			
		<div id="side-info-column" class="inner-sidebar">
			<?php do_meta_boxes( $pagehook, 'side', $this ); ?>
		</div>
		<div id="post-body" class="has-sidebar">
			<div id="post-body-content" class="has-sidebar-content">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<?php do_meta_boxes( $pagehook, 'normal', $this ); ?>
					<?php //do_meta_boxes( $pagehook, 'advanced', $this ); ?>
					
					<table class="widefat">
						<thead>
							<tr>
								<th colspan="2">Owl in charge</th>
								<th>Plugin</th>
								<th style="text-align:center; width:110px;">Install</th>
								<th style="text-align:center; width:110px;">Activate</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th colspan="2">Owl in charge</th>
								<th>Plugin</th>
								<th style="text-align:center;">Install</th>
								<th style="text-align:center;">Activate</th>
							</tr>
						</tfoot>
						<tbody>
							<?php
							foreach( $this->m_owls as $key => $value )
							{
								//$data = get_plugin_data( WP_PLUGIN_DIR.'/'.$value['plugin_url'], false );
								
								//if( $data['Name'] )
								if( file_exists( WP_PLUGIN_DIR.'/'.$value['plugin_url'] ) )
									$installed = true;
								else
									$installed = false;
								
								if( $installed && is_plugin_active( $value['plugin_url'] ) )
									$activated = true;
								else
									$activated = false;
								
								$show_activate_button = 'none'; ?>
							<tr>
								<td style="width:80px;">
									<img style="width:80px;" src="<?= $value['image_url']; ?>" />
								</td>
								<td style="vertical-align:middle;">
									<a target="_new" href="<?= $value['owl_url']; ?>" style="font-size:18px; font-weight:bold;"><?= $value['owl_name']; ?></a>
								</td>
								<td style="vertical-align:middle;">
									<?php
									$name = '<span class="plugin_name">'.$value['plugin_name'].'</span>';
									
									if( $value['wordpress_url'] )
										$name = '<a href="'.$value['wordpress_url'].'">'.$name."</a>";
									
									echo $name; ?>
								</td>
								<td style="vertical-align:middle; text-align:center;">
									<?php
									if( $installed )
									{ ?>
									<span class="installed">installed</span>
									<?php										
									}
									else
									{ ?>
									<p class="submit" style="float:none;">
										<input class="plugin_install" rel="<?= $value['download_url']; ?>" type="submit" name="submit" value="Click to Install »">
									</p>
									<?php
									} ?>
								</td>
								<td style="vertical-align:middle; text-align:center;">
									<?php
									if( $activated )
									{ ?>
									<span class="activated">activated</span>
									<?php										
									}
									else if( $installed )
									{
										$show_activate_button = 'block';
									}
									else
									{
										$show_activate_button = 'none';
									} ?>
									<p class="submit" style="float:none; display:<?= $show_activate_button; ?>;">
										<input class="plugin_activate" rel="<?= $value['plugin_url']; ?>" type="submit" name="submit" value="Activate plugin »">
									</p>
								</td>
							</tr>
							<?php
							} ?>
							
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

</div>
	
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready( function($) {
	// close postboxes that should be closed
	$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
	// postboxes setup
	postboxes.add_postbox_toggles('<?= $pagehook; ?>');
});
//]]>
</script>