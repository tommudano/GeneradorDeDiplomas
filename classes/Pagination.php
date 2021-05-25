<?php
class Pagination {
    private $_db;
    public $_current_page;
    public $_per_page;
    public $_count;
    
    public function __construct($page=1, $per_page=15, $total) {
        $this->_db = DB::getInstance();
        $this->_current_page = (int)$page;
        $this->_per_page = (int)$per_page;
        $this->_count = (int)$total;
    }
	
	public function offset(){
		return ($this->_current_page - 1) * $this->_per_page;
	}
	
	public function total_pages(){
		return ceil($this->_count/$this->_per_page);
	}
	
	public function previous_page(){
		return $this->_current_page - 1;
    }
    
	public function has_previous_page(){
        return $this->previous_page() >= 1 ? true : false;
    }
	
    public function next_page(){		
        return $this->_current_page + 1;
    }
	
	public function has_next_page(){
		return $this->next_page() <= $this->total_pages() ? true : false;
    }
    
    public function generatePaginate() {
        $paginationData = array(
            'per_page' => $this->_per_page,
            'offset' => $this->offset(),
            'total' => $this->total_pages()
        );

        return $paginationData;
    }
}