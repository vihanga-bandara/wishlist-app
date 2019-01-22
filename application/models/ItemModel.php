<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 1/20/2019
 * Time: 9:45 AM
 */
class ItemModel extends CI_Model
{

	public function getItem($item_id)
	{
		$this->db->select("*");
		$this->db->from("item_tbl");
		$this->db->where("item_id", $item_id);
		$query = $this->db->get();
		return $query->result();
	}

	public function getAllItems($user_id)
	{
		$this->db->select("*");
		$this->db->from("item_tbl");
		$this->db->where('user_id', "1");
		$query = $this->db->get();
		return $query->result();
	}

	public function addItem($postData)
	{
		$this->db->insert('item_tbl',$postData);
		return $this->db->insert_id();
	}

	public function updateItem($item_name, $item_description, $item_url, $item_price, $item_priority, $item_id)
	{
		$this->db->set('item_name', $item_name);
		$this->db->set('item_description', $item_description);
		$this->db->set('item_price', $item_price);
		$this->db->set('item_url', $item_url);
		$this->db->set('item_priority', $item_priority);
		$this->db->where("item_id", $item_id);
		$this->db->update('item_tbl');
		return ($this->db->affected_rows() >= 1) ? "Updating Item was successful" : "Error Updating Item";
	}

	public function deleteItem($item_id)
	{
		$this->db->where("item_id", $item_id);
		$this->db->delete("item_tbl");
		return ($this->db->affected_rows() >= 1) ? "Deleted Successfully" : "Item Not Found";
	}
}
