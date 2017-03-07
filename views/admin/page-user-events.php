<div class="wrap wrap-auditor">
	<h3><?php _e('User Events', 'auditor'); ?></h3>
	<table class="widefat auditor-events-table">
		<thead>
			<tr>
				<th><?php _e('User', 'auditor'); ?></th>
				<th><?php _e('Activity', 'auditor'); ?></th>
				<th><?php _e('Meta', 'auditor'); ?></th>
				<th><?php _e('Date', 'auditor'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($events as $event): ?>
			<tr class="auditor-event">
				<td classs="col-user">
					<a href="<?php echo admin_url("user-edit.php?user_id={$event->user->ID}"); ?>">
						<?php echo esc_html($event->user->display_name); ?>
					</a>
				</td>
				<td class="col-activity">
					<?php echo esc_html($event->activity); ?>
				</td>
				<td class="col-meta">
					<?php foreach ($event->meta as $name => $value): ?>
					<span class="auditor-meta-info meta-<?php esc_attr($name); ?>">
						<strong><?php echo esc_html($name); ?></strong>
						<?php echo esc_html($value); ?>
					</span>
					<?php endforeach; ?>
				</td>
				<td class="col-created-at">
					<?php echo esc_html($event->created_at); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>