<div class="row">
	<div class="span12">
		<?php echo Html::anchor('hosts/create', '<i class="icon-book"></i> Create', array('class' => 'btn btn-small btn-success pull-right')); ?>
	</div>

	<div class="span12 rawr">
		<?php if ($hosts): ?>
			<table class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th>Title</th>
						<th>Password</th>
						<th>Link</th>
						<th>Database</th>
						<th>Created by</th>
						<th>Created at</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
		<?php foreach ($hosts as $key => $value): ?>
					<tr>
						<td><?php echo $value->title; ?></td>
						<td><?php echo $value->password; ?></td>
						<td><a href="<?php echo 'http://'.$value['title'].'.pepperit.lt'; ?>"><?php echo $value['title']; ?></a></td>
						<td><a href="<?php echo 'http://'.$value['title'].'.pepperit.lt/phpmyadmin'; ?>">Database</a></td>
						<td><?php echo $value['first_name'].' '.$value['last_name']; ?></td>
						<td><?php echo Date::forge($value['created_at'])->format('%Y-%m-%d'); ?></td>
						<td>
							<?php echo Html::anchor('hosts/delete/'.$value['id'], '<i class="icon-trash"></i> Delete', array('class' => 'btn btn-small btn-danger')); ?>
						</td>
					</tr>
		<?php endforeach ?>
				</tbody>
			</table>
		<?php else: ?>
			<div class="alert alert-error">
				Sorry we cannot find any records
			</div>
		<?php endif ?>
	</div>
</div>