<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WorkUpdateTimelineTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::unprepared('CREATE TRIGGER add_work_timeline_trigger AFTER UPDATE ON service_work FOR EACH ROW
                BEGIN
			INSERT INTO work_history (Work_ID,Lead_ID, Comments, WorkStatus) VALUES (NEW.Work_ID, NEW.Lead_ID,,NEW.Comments, NEW.WorkStatus);
				  
				   IF(NEW.QuotationDate) THEN
				    INSERT INTO work_timeline (Work_ID, Work_Attrb_ID, Value) VALUES (NEW.Work_ID, 7, NEW.QuotationDate);END IF;
				    IF(NEW.Site_Analysis_Date) THEN
				    INSERT INTO work_timeline (Work_ID, Work_Attrb_ID, Value) VALUES (NEW.Work_ID, 8, NEW.Site_Analysis_Date); END IF;
                    IF(NEW.ActualSite_Analysis_Date) THEN
				    INSERT INTO work_timeline (Work_ID, Work_Attrb_ID, Value) VALUES (NEW.Work_ID, 2, NEW.ActualSite_Analysis_Date); END IF;
					 
                     
                     END');
					  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
          \DB::unprepared('DROP TRIGGER `add_work_timeline_trigger`');
    }
}
