<?php
defined('_JEXEC') or die;
/*
 * The list is displayed in a form.
 * The form is submitted when the view filters change or pagination is clicked.
 * The form's 'action' attribute is set to a url that is passed through JRoute.
 * JRoute helps with routing and allows Joomla to keep track of the currently active menu item.
 * The form is submitted to this view (which is the default view).
 * The 'name' and 'id' attributes must be 'adminForm' for the save and close buttons to work
 */

//  get the current user for pub_state setting auth below
$user = JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder   = $user->authorise('core.edit.state','com_restaurant.category');
$saveOrder  = $listOrder == 'r.ordering';
if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_restaurant&task=restaurants.saveOrderAjax&tmpl=component';
    // note that restauratnList = id of the table and adminForm = id of the enclosing form
    JHtml::_('sortablelist.sortable','restaurantList','adminForm',strtolower($listDirn),$saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<!-- this is standard javaScript needed by the ordering column -->
<script type="text/javascript">
    Joomla!.orderTable = function(){
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>'){
            dirn = 'asc';
        }
        else {
            dirn = direction.options[direction.selectedIndex].value;
        } 
        Joomla!tableOrdering(order, dirn, '')
    }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_restaurant&view=restaurants'); ?>" method="post" name="adminForm" id="adminForm">
    <!-- 
        show the sidebar if it's not empty
    -->
    <?php if(!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container", class="span2">
            <?php echo $this->sidebar; ?>
        </div>
	    <div id="j-main-container" class="span10">
	<?php else : ?>
	   <div id="j-main-container">
	<?php endif; ?>
	<div id="filter-bar" class="btn-toolbar">
	    <div class="filter-search btn-group pull-left">
	        <label for="filter-search" class="element-invisible">
	            <?php echo JText::_('COM_RESTAURANT_SEARCH_IN_RESTAURANT'); ?>
	        </label>
	        <input type="text" name="filter_search" id="filter_search" 
	           placeholder="<?php echo JText::_('COM_RESTAURANT_SEARCH_IN_RESTAURANT'); ?>"
	           value="<?php echo $this->escape($this->state->get('filter.search')); ?>" 
	           title="<?php echo JText::_('COM_RESTAURANT_SEARCH_IN_RESTAURANT'); ?>" />
	    </div>
	    <div class="btn-group pull-left">
	        <button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
	            <i class="icon-search"></i>
	        </button>
            <button class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
                onclick="document.id('filter_search').value=''; this.form.submit();" >
                <i class="icon-remove"></i>
            </button>
	    </div>
	    <div class="btn-group pull-right hidden-phone">
	        <label for="limit" class="element-invisible">
	            <?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
	        </label>
	        <?php echo $this->pagination->getLimitBox(); ?>
	    </div>
	    <div class="btn-group pull-right hidden-phone">
	        <label for="directionTable" class="element-invisible">
	            <?php echo JText::_('JFIELD_ORDERING_DESC'); ?>
	        </label>
	        <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla!.orderTable()">
	            <option value="">
	                <?php echo JText::_('JFIELD_ORDERING_DESC'); ?>
	            </option>
	            <option value="asc" <?php if($listDirn=='asc') echo 'selected = "selected"'; ?>>
	                <?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?>
	            </option>
	            <option value="desc" <?php if($listDirn=='desc') echo 'selected = "selected"'; ?>>
                    <?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?>
                </option>
	        </select>
	    </div>
	    <div class="btn-group pull-right hidden-phone">
            <label for="sortTable" class="element-invisible">
                <?php echo JText::_('JGLOBAL_SORT_BY') ?>
             </label>
             <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla!.orderTable()">
                <option value="">
                    <?php JText::_('JGLOBAL_SORT_BY'); ?>
                </option> 
                <!-- 
                   the JHtml method 'select.options' generates options based on the 
                   associative array $sortFields. 'value' and 'text' are the default values
                   of the methods optKey and optText parameters respectively. 
                   $listOrder is the selected item    
                -->   
                <?php echo JHtml::_('select.options',$sortFields,'value','text',$listOrder); ?>
             </select>
        </div>
	</div>
	   <div class="clearfix"> </div>
		<table class="table table-striped" id="restaurantList">
			<thead>
				<tr>
				    <th width="1%" class="nowrap center hidden-phone">
				        <?php echo JHtml::_('grid.sort','<i class="icon-menu-2></i>"','r.ordering',$listDirn,$listOrder,null,'asc','JGRID_HEADING_ORDERING'); ?>
				    </th>
				    <!--check all checkbox-->
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="1%" style="min-width: 55px" class="nowrap center">
				        <?php echo JHtml::_('grid.sort', 'JSTATUS', 'r.pub_state', $listDirn, $listOrder); ?>
					</th>
					<th class="title">
						<?php echo JHtml::_('grid.sort', 'COM_RESTAURANT_HEADING_RESTAURANT', 'r.restaurant', $listDirn, $listOrder); ?>
					</th>
                    <th width="10%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_RESTAURANT_HEADING_NEIGHBORHOOD','n.name', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap">
                        <?php echo JHtml::_('grid.sort', 'COM_RESTAURANT_HEADING_PHONE','r.phone', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap">
                        <?php echo JHtml::_('grid.sort', 'COM_RESTAURANT_HEADING_ADDRESS1','r.address1', $listDirn, $listOrder); ?>
                    </th>                   
                    <th width="10%" class="nowrap">
                        <?php echo JHtml::_('grid.sort', 'COM_RESTAURANT_HEADING_ZIP','r.zip', $listDirn, $listOrder); ?>
                    </th>                   
				    <th width="10%" class="nowrap">
                        <?php echo JHtml::_('grid.sort', 'COM_RESTAURANT_HEADING_DISPLAY_LOGO','r.display_logo', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap">
                        <?php echo JHtml::_('grid.sort', 'COM_RESTAURANT_HEADING_WEBSITE','r.website', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap">
                        <?php echo JHtml::_('grid.sort', 'COM_RESTAURANT_HEADING_BLURB','r.blurb', $listDirn, $listOrder); ?>
                    </th>
				</tr>
			</thead>
			<tfoot>
			    <tr>
			        <td colspan="10">
			            <?php echo $this->pagination->getListFooter(); ?>
			        </td>
			    </tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item) : 
                /*
                 * unauthorised users will see greyed out status buttons
                 * see jgrid.published call in second td below
                 */
			    $canCheckin = $user->authorise('core.manage','com_checkin')
                    || $item->$checked_out == $user->get('id') 
                    || $item->$checked_out == 0;
                $canChange = $user->authorise('core.edit.state', 'com_restaurant')
                    && $canCheckin;
                $canEdit    = $user->authorise('core.edit',       'com_restaurant.category.' . $item->catid);
                 ?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
				    <!-- this is the ordering row, the code is standard -->
				    <td class="order nowrap center hidden-phone">
				        <?php if ($canChange) :
                            $disableClassName = '';
                            $disabledLabel = '';
                            if (!$saveOrder) :
                                $disabledLabel = JText::_('JORDERINGDISABLED');
                                $disableClassName = 'inactive tip-top';
                            endif; ?>
                            <span class="sortable-handler hasToolTip <?php echo $disableClassName ?>" 
                                title="<?php echo $disabledLabel ?>">
                                <i class="icon-menu"></i>
                            </span>
                            <input type="text" style="display:none" 
                                name="order[]" size="5" 
                                value="<?php echo $item->ordering; ?>" 
                                class="width-20 text-area-order " />
                        <?php else : ?>
                            <span class="sortable-handler inactive" >
                                <i class="icon-menu"></i>
                            </span>
                        <?php endif; ?>
				    </td>
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center">
					    <?php echo JHtml::_('jgrid.published',$item->pub_state,$i,'restaurants.',$canChange,'cb',$item->publish_up,$item->publish_down); ?>					   
					</td>
					<td class="nowrap has-context">
					    <?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_restaurant&task=restaurant.edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->restaurant); ?>
						</a>
						<?php else : ?>
                            <?php echo $this->escape($item->neighborhood); ?>
                        <?php endif; ?>
					</td>
                    <td class="center hidden-phone">
                        <?php echo $this->escape($item->name); ?>
                    </td>
                    <td>
                        <?php echo $this->escape($item->phone); ?>
                    </td>
                    <td class="center">
                        <?php echo $this->escape($item->address1); ?>
                    </td>
                    <td class="center">
                        <?php echo $this->escape($item->zip); ?>
                    </td>	
                    <td class="center">
                        <?php echo $this->escape($item->display_logo); ?>
                    </td>
                    <td class="center">
                        <?php echo $this->escape($item->website); ?>
                    </td>
                    <td class="center">
                        <?php echo $this->escape($item->blurb); ?>
                    </td>				
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>