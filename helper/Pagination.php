<?php

/**
 * Created by PhpStorm.
 * User: luvyatyss
 * Date: 12/9/2015
 * Time: 10:04 PM
 */
class Pagination
{
    var $curPage, $rowsPerPage, $offset, $numberOfRows, $self;

    function __construct( $curPage , $rowsPerPage, $offset, $numberOfRows , $self = "" )
    {
        $this->curPage = $curPage;
        $this->rowsPerPage = $rowsPerPage;
        $this->offset = $offset;
        $this->numberOfRows = $numberOfRows;
        $this->$self = $self;
    }

    public function getCurPage()
    {
        return $this->curPage;
    }

    public function getRowsPerPage()
    {
        return $this->rowsPerPage;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getNumberOfRows()
    {
        return $this->numberOfRows;
    }

    public function setCurPage($curPage)
    {
        $this->curPage = $curPage;
    }

    public function setRowsPerPage($rowsPerPage)
    {
        $this->rowsPerPage = $rowsPerPage;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function setNumberOfRows($numberOfRows)
    {
        $this->numberOfRows = $numberOfRows;
    }

    public function numberOfPage()
    {
        return ceil($this->numberOfRows / $this->rowsPerPage); //Làm tròn lên
    }

    public function  printPaging()
    {
        $nav = ""; //Chuỗi các liên kết đến trang con
        $numberOfPage = $this->numberOfPage();
        for ($page = 1; $page <= $numberOfPage; $page++) {
            if ($page == $this->curPage) {
                $nav .= "<li class='active'><span>$page</span></li>"; //Không cần tao liên kết cho trang hiện hàng
            } else {
                $nav .= "<li><a>$page</a></li>";
            }
        }//Tạo liên kết Trang đầu | Trang trước | ...
        if ($this->curPage > 1) {
            $page = $this->curPage - 1;

            $prev = "<li><a><span><span class=\"sr-only\">$page</span><i class=\"fa fa-angle-left\"></i></span></a></li>";
            $first = "<li><a><span><span class=\"sr-only\">1</span><i class=\"fa fa-angle-double-left\"></i></span></a></li>";
        } else {

            $prev = "<li class=\"disabled\"><span aria-hidden=\"true\"><i class=\"fa fa-angle-left\"></i></span></li>";
            $first = "<li class=\"disabled\"><span aria-hidden=\"true\"><i class=\"fa fa-angle-double-left\"></i></span></li>";

        }
        if ($this->curPage < $numberOfPage) {

            $page = $this->curPage + 1;
            $next = "<li><a><span><span class=\"sr-only\">$page</span><i class=\"fa fa-angle-right\"></i></span></a></li>";
            $last = "<li><a><span><span class=\"sr-only\">$numberOfPage</span><i class=\"fa fa-angle-double-right\"></i></span></a></li>";
        } else {

            $next = "<li class=\"disabled\"><span aria-hidden=\"true\"><i class=\"fa fa-angle-right\"></i></span></li>";
            $last = "<li class=\"disabled\"><span aria-hidden=\"true\"><i class=\"fa fa-angle-double-right\"></i></span></li>";

        }
        echo  "<ul class=\" pagination pull-right\">" . $first . $prev . $nav . $next . $last . "</ul>";
    }

}