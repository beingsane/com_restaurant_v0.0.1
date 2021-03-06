<?php
defined('_JEXEC') or die;

class RestaurantModelRestaurants extends JModelList
{
	public function __construct($config = array())
	{
	    /*
         * 'filter_fields' is an array/whitelist of all the fields 
         * being used by the view  
        */
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id'
				,'r.id'
				,'restaurant'
				,'r.restaurant'
				,'pub_state'
				,'r.pub_state'
				,'neighborhood_id'
				,'r.neighborhood_id'
				,'name'
				,'n.name'
				,'address1'
				,'r.address1'
				,'address2'
				,'r.address2'
				,'city'
				,'r.city'
				,'state'
				,'r.state'
				,'zip'
				,'r.zip'
				,'phone'
				,'r.phone'
				,'fax'
				,'r.fax'
				,'display_logo'
				,'r.display_logo'
				,'website'
				,'r.website'
				,'blurb'
				,'r.blurb'
				,'publish_up'
				,'r.publish_up'
				,'publish_down'
				,'r.publish_down'
				,'ordering'
				,'r.ordering'
				,'catid'
				,'r.catid'
				,'category_title'
			);
		}
		parent::__construct($config);
	}
    
    /*
     * sets the default column on which to sort the view
     * populates list.ordering and list.direction among other things
     */
    protected function populateState($ordering=null, $direction = null)
    {
        /*
         * find out which option of the 'status' filter is selected, and assign it 
         * to a variable for use in the query below
         */
        $published = $this->getUserStateFromRequest($this->context.'.filter.state','filter_state','','string');
        $this->setState('filter.state',$published);
        $search=$this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search',$search);
        parent::populateState('r.ordering', 'asc');
    }

    /**
    * Method to get a JDatabaseQuery object for retrieving the data set from a database.
    *
    * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
    *
    * @since   12.2
    */
	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
        /*
         * gettState() checks if the state variable in the 
         * first arguement ('list.select' in this case)
         * has been defined, otherwise it defaults to the 
         * select list string in the second arguement
         */
		$query->select(
			$this->getState(
				'list.select',
				'r.id, r.restaurant, r.catid, '.
				'r.pub_state,n.name,r.address1,'.
                'r.address2,r.city,r.state,r.zip,r.phone,'.                
                'r.fax,r.display_logo,r.website,r.blurb,'.
                'r.publish_up,r.publish_down,r.ordering'
			)
		);
        /*
         * build the from clause
         */
		$query->from($db->quoteName('#__rl_disp_restaurant_list','r'));
        $query->join('LEFT',$db->quoteName('#__rl_neighborhood','n').
        ' ON ('.$db->quoteName('r.neighborhood_id').' = '.$db->quoteName('n.id').')');    
        
        $published = $this->getState('filter.state');
        if (is_numeric($published))
        {
            $query->where('r.pub_state = '.(int)$published);
        } elseif ($published === '')
        {
            $query->where('(r.pub_state IN (0,1))');
        }
        
        // join over categories
        $query->select('c.title AS category_title');
        $query->join('LEFT', '#__categories AS c ON c.id = r.catid');
        
        //  filter by search in restaurant
        $search = $this->getState('filter.search');
        if (!empty($search)) {            
            if (stripos($search, 'id:')===0) {
                $query->where('r.id='.(int)substr($search, 3));
            }else {
                $search =  $db->quote('%'.$db->escape($search,TRUE).'%');
                $query->where('(r.restaurant like '.$search.' or r.blurb like '.$search.')');
            }
        }
        /*
         * get the order and direction from the model state
         */
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        
        if ($orderCol == 'r.ordering')
        {
            $orderCol = 'r.neighborhood '.$orderDirn.', r.ordering';
        }   
        
        $query->order($db->escape($orderCol.' '.$orderDirn));                                                                                                          

		return $query;
	   }
    }