<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLeadTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::unprepared('CREATE TRIGGER update_lead_trigger AFTER UPDATE ON sales_lead FOR EACH ROW
                BEGIN
				
                   INSERT INTO lead_history (Lead_ID,Activity,Comment) VALUES (NEW.Lead_ID,NEW.Activity,NEW.Comment);
                   IF(NEW.NxtFollowupDate != OLD.NxtFollowupDate) THEN
				    UPDATE lead_history l SET l.NxtFollowupDate = NEW.NxtFollowupDate WHERE l.Lead_ID=NEW.Lead_ID ;
                    ELSE IF (NEW.AssginedTo != OLD.AssginedTo) THEN
				    UPDATE lead_history l  SET l.AssginedTo = NEW.AssginedTo WHERE l.Lead_ID=NEW.Lead_ID;
                    ELSE IF(NEW.Priority != OLD.Priority) THEN
				    UPDATE lead_history l SET l.Priority = NEW.Priority WHERE l.Lead_ID=NEW.Lead_ID ;
                    ELSE IF(NEW.Priority != OLD.Priority) THEN
				    UPDATE lead_history l SET l.Priority = NEW.Priority WHERE l.Lead_ID=NEW.Lead_ID ;
                    End IF;
                    END IF;
				   
				   END IF;
				   
				   END IF;
				 
                END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::unprepared('DROP TRIGGER `update_lead_trigger`');
    }
}
