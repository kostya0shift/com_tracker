<?php
/**
 * @version			2.5.0
 * @package			Joomla
 * @subpackage	com_tracker
 * @copyright		Copyright (C) 2007 - 2012 Hugo Carvalho (www.visigod.com). All rights reserved.
 * @license			GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.tooltip');

$user		= $this->user;
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'a.ordering';
?>

<form action="<?php echo JRoute::_('index.php?option=com_tracker&view=comments'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true);?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%" class='left' nowrap><?php echo JHtml::_('grid.sort',	'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?></th>

				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>

				<th width="1%" class='right' nowrap><?php echo JHtml::_('grid.sort',	'COM_TRACKER_COMMENT_TORRENTID', 'torrentID', $listDirn, $listOrder); ?></th>

				<th class='right'><?php echo JHtml::_('grid.sort',	'COM_TRACKER_COMMENT_TORRENTNAME', 'torrent', $listDirn, $listOrder); ?></th>

				<th class='left'><?php echo JHtml::_('grid.sort',	'COM_TRACKER_COMMENT_COMMENT', 'comment', $listDirn, $listOrder); ?></th>

				<th class='left'><?php echo JHtml::_('grid.sort',	'JGLOBAL_FIELD_CREATED_BY_LABEL', 'username', $listDirn, $listOrder); ?></th>

				<th class='left'><?php echo JHtml::_('grid.sort',	'JGLOBAL_CREATED_DATE', 'a.created_time', $listDirn, $listOrder); ?></th>

				<th width="10%">
					<?php echo JHtml::_('grid.sort',	'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($saveOrder) :?>
						<?php echo JHtml::_('grid.order',	$this->items, 'filesave.png', 'comments.saveorder'); ?>
					<?php endif; ?>
				</th>

				<th width="5%"><?php echo JHtml::_('grid.sort',	'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?></th>

			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'a.ordering');
			$canCreate	= $user->authorise('core.create',		'com_tracker');
			$canEdit	= $user->authorise('core.edit',			'com_tracker');
			$canCheckin	= $user->authorise('core.manage',		'com_tracker');
			$canChange	= $user->authorise('core.edit.state',	'com_tracker');
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td width="1%" nowrap><?php echo $item->id;?></td>

				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>

				<td class="center">
					<?php echo $item->torrentID; ?>
				</td>
				<?php if (isset($this->items[0]->torrent)) { ?>
				<td class="center">
					<?php if ($canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_tracker&task=comment.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($item->torrent); ?></a>
					<?php else : ?>
					<?php echo $this->escape($item->torrent); ?>
					<?php endif; ?>
				</td>
				<?php } ?>
				
				<td><?php echo $this->escape($item->comment); ?></td>

				<td><?php echo $this->escape($item->username); ?></td>
				
				<td><?php echo date('Y.m.d', strtotime($item->created_time)); ?></td>
				
				<td class="order">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) :?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, 1 == 1, 'comments.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, (1 == 1), 'comments.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, (1 == 1), 'comments.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, (1 == 1), 'comments.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>

				<td class="center"><?php echo JHtml::_('grid.boolean', $i, $item->state, 'comments.state', 'comments.no_state'); ?></td>

			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>