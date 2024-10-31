
<div style="padding: 5px 25px 25px 25px;">
	
	<p>Welcome! Here you can hire and manage Qody's owls and their plugins. Simply use the fields below to 
	get started.</p>

	<table class="widefat">
		<thead>
			<tr>
				<th colspan="2">Owl Overview</th>
				<th>Plugin</th>
				<th>Install</th>
				<th>Updates</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th colspan="2">Owl Overview</th>
				<th>Plugin</th>
				<th><span class="total_custom">Install</span></th>
				<th>Updates</th>
			</tr>
		</tfoot>
		<tbody>
			<?php
			foreach( $this->m_owls as $key => $value )
			{ ?>
			<tr>
				<td style="width:80px;">
					<img style="width:80px;" src="<?= $value['image_url']; ?>" />
				</td>
				<td style="vertical-align:middle;">
					<a target="_new" href="<?= $value['owl_url']; ?>" style="font-size:18px; font-weight:bold;"><?= $value['owl_name']; ?></a>
				</td>
				<td style="vertical-align:middle;">
					<?= $value['plugin_name']; ?>
				</td>
				<td style="vertical-align:middle;">
					<p class="submit" style="float:none;"><input type="submit" name="submit" value="Click to Install »"></p>
				</td>
				<td style="vertical-align:middle;">
					none
				</td>
			</tr>
			<?php
			} ?>
			
		</tbody>
	</table>
	
</div>