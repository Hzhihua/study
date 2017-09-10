<?php
	
	class Page {
		private $total; //数据表中总记录数
		private $listRows; //每页显示行数
		private $limit;
		private $uri;
		private $pageNum; //页数
		private $config=array('header'=>"个记录", "prev"=>"上一页", "next"=>"下一页", "first"=>"首 页", "last"=>"尾 页");
		private $listNum=8;
		/*
		 * $total 
		 * $listRows
		 */
		public function __construct($total, $listRows=10, $pa=""){
			$this->total=$total;
			// $this->listRows=$listRows;
			$this->listRows=$_GET['row'] ? (int)$_GET['row'] : $listRows;;
			$this->uri=$this->getUri($pa);
			$this->page=!empty($_GET["page"]) ? $_GET["page"] : 1;
			$this->pageNum=ceil($this->total/$this->listRows);
			// 确保输入跳转的页数不会超过实际的页数
			$this->page = $this->page > $this->pageNum ? $this->pageNum : $this->page;
			$this->limit=$this->setLimit();
		}

		private function setLimit(){
			// return "Limit ".($this->page-1)*$this->listRows.", {$this->listRows}";
			$start = ($this->page-1)*$this->listRows;
			$start = $start < 0 ? 0 : $start;   // 确保开始查询的行数数据不会出现负数
			$end = $this->listRows;
			return $start . ', ' . $end;
		}

		private function getUri($pa){
			$url=$_SERVER["REQUEST_URI"].(strpos($_SERVER["REQUEST_URI"], '?')?'':"?").$pa;
			$parse=parse_url($url);

			if(isset($parse["query"])){
				parse_str($parse['query'],$params);
				unset($params["page"]);
				unset($params["row"]);
				$url=$parse['path'].'?'.http_build_query($params);
				
			}

			return $url;
		}

		function __get($args){
			if($args=="limit")
				return $this->limit;
			else
				return null;
		}

		private function start(){
			if($this->total==0)
				return 0;
			else
				return ($this->page-1)*$this->listRows+1;
		}

		private function end(){
			return min($this->page*$this->listRows,$this->total);
		}

		private function first(){
            $html = "";
			if($this->page==1)
				$html.='';
			else
				$html.="&nbsp;<a href='{$this->uri}&page=1&row={$this->listRows}'>{$this->config["first"]}</a>&nbsp;";

			return $html;
		}

		private function prev(){
            $html = "";
			if($this->page==1)
				$html.='';
			else
				$html.="&nbsp;<a href='{$this->uri}&page=".($this->page-1)."&row=".$this->listRows."'>{$this->config["prev"]}</a>&nbsp;";

			return $html;
		}

		private function pageList(){
			$linkPage="";
			
			$inum=floor($this->listNum/2);
		
			for($i=$inum; $i>=1; $i--){
				$page=$this->page-$i;

				if($page<1)
					continue;

				$linkPage.="&nbsp;<a href='{$this->uri}&page={$page}&row={$this->listRows}'>{$page}</a>&nbsp;";

			}
		
			$linkPage.= $this->page;
			

			for($i=1; $i<=$inum; $i++){
				$page=$this->page+$i;
				if($page<=$this->pageNum)
					$linkPage.="&nbsp;<a href='{$this->uri}&page={$page}&row={$this->listRows}'>{$page}</a>&nbsp;";
				else
					break;
			}

			return $linkPage;
		}

		private function next(){
            $html = "";
			if($this->page==$this->pageNum)
				$html.='';
			else
				$html.="&nbsp;<a href='{$this->uri}&page=".($this->page+1)."&row=".$this->listRows."'>{$this->config["next"]}</a>&nbsp;";

			return $html;
		}

		private function last(){
            $html = "";
			if($this->page==$this->pageNum)
				$html.='';
			else
				$html.="&nbsp;<a href='{$this->uri}&page=".($this->pageNum)."&row=".$this->listRows."'>{$this->config["last"]}</a>&nbsp;";

			return $html;
		}

		private function goPage(){
			return '&nbsp;<input type="text" onkeydown="javascript:if(event.keyCode==13){var page=(this.value>'.$this->pageNum.')?'.$this->pageNum.':this.value;location=\''.$this->uri.'&page=\'+page+\'\'}" value="'.$this->page.'" style="width:25px"><input type="button" value="GO" onclick="javascript:var page=(this.previousSibling.value>'.$this->pageNum.')?'.$this->pageNum.':this.previousSibling.value;location=\''.$this->uri.'&page=\'+page+\'&row='.$this->listRows.'\'">&nbsp;';
		}

		private function goLimit(){
			return '&nbsp;<input type="text" value="'.$this->listRows.'" style="width:25px"><input type="button" value="GO" onclick="javascript:var row=this.previousSibling.value;location=\''.$this->uri.'&page='.$this->page.'&row=\'+row+\'\'">&nbsp;';
		}

		function fpage($display=array(0,1,2,3,4,5,6,7,8)){
			$html[]="&nbsp;共有<b>{$this->total}</b>{$this->config["header"]}&nbsp;";
			$html[]="每页显示<b>".$this->goLimit()."</b>条，本页<b>{$this->start()}-{$this->end()}</b>条&nbsp;";
			$html[]="<b>{$this->page}/{$this->pageNum}</b>页&nbsp;";
			
			$html[]=$this->first();
			$html[]=$this->prev();
			$html[]=$this->pageList();
			$html[]=$this->next();
			$html[]=$this->last();
			$html[]=$this->goPage();
			$fpage='';
			foreach($display as $index){
				$fpage.=$html[$index];
			}

			return $fpage;

		}

	
	}
