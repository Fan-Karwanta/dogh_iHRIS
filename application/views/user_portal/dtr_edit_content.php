<?php
$days_in_month = date("t", mktime(0, 0, 0, $selected_month, 1, $selected_year));
$dtr_by_date = [];
foreach ($dtr_records as $rec) { $dtr_by_date[date("j", strtotime($rec->date))] = $rec; }
?>
