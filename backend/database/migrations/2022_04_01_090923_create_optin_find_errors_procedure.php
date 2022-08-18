<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOptinFindErrorsProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dropProcedure ="DROP PROCEDURE IF EXISTS optin_find_errors;";
        DB::connection('mysql2')->unprepared($dropProcedure);

        $procedure ="CREATE DEFINER=`root`@`localhost` PROCEDURE `optin_find_errors`(IN `V_ID_CLIENT` bigint, IN `V_ID_CAMPAIGN` int, IN `V_MAILING_FILE_ORIGINAL` varchar(100), IN `V_MAILING_FILE_GENION` varchar(100), IN `V_JUST_SMS` int)
                BEGIN

                    DECLARE V_MAILING_FILE VARCHAR(10);
                    DECLARE V_ID  bigint;
                    DECLARE V_DDD  int;
                    DECLARE V_PHONE  VARCHAR(20);
                    DECLARE V_MESSAGE_SMS  LONGTEXT;
                    DECLARE V_DATE_EVENT  VARCHAR(50);
                    DECLARE V_TITLE  VARCHAR(255);
                    DECLARE V_DESCRIPTION  LONGTEXT;
                    DECLARE V_LOCATION  VARCHAR(255);
                    DECLARE V_IDENTIFICATION  VARCHAR(255);
                    DECLARE V_JOKER_ONE  VARCHAR(255);
                    DECLARE V_JOKER_TWO  VARCHAR(255);
                    DECLARE V_done INT DEFAULT FALSE;
                    DECLARE V_TOTAL INT DEFAULT 0;
                    DECLARE V_finished int;
                    DECLARE V_fields_errors VARCHAR(255);
                    DECLARE V_qtd_errors int;

                    DECLARE V_errors_phone int;
                    DECLARE V_phone_blacklist int;
                    DECLARE V_ddd_errors int;
                    DECLARE V_DDD_PHONE varchar(20);


                    DECLARE v_mailing
                        CURSOR FOR
                            SELECT id, ddd, phone, CONCAT(ddd, phone) as ddd_phone, message_sms, date_event, title, description, location, identification, joker_one, joker_two
                            FROM mailing_process
                            WHERE id_client = V_ID_CLIENT
                                AND id_campaign = V_ID_CAMPAIGN
                                AND mailing_file_original = V_MAILING_FILE_ORIGINAL
                                AND mailing_file_genion = V_MAILING_FILE_GENION
                                AND confirm_imported = 0;

                    DECLARE CONTINUE HANDLER FOR NOT FOUND SET V_finished = 0;


                    UPDATE blacklist set phone = TRIM(REPLACE(REPLACE(phone , CHAR(13), ''), CHAR(10),''))
                        WHERE id_client = V_ID_CLIENT AND active = 1 AND deleted_at IS NULL AND DATE(created_at) = DATE(NOW()) ;

                    OPEN v_mailing;

                        get_mailing: LOOP

                            FETCH v_mailing INTO V_ID, V_DDD, V_PHONE, V_DDD_PHONE, V_MESSAGE_SMS, V_DATE_EVENT, V_TITLE, V_DESCRIPTION, V_LOCATION, V_IDENTIFICATION, V_JOKER_ONE, V_JOKER_TWO;

                            SET V_fields_errors = '';
                            SET V_qtd_errors = 0;

                            SET V_ddd_errors = 0;

                            IF  V_DDD = 68 or
                                V_DDD = 82 or
                                V_DDD = 92 or V_DDD = 97 or
                                V_DDD = 96 or
                                V_DDD = 71 or V_DDD = 73 or V_DDD = 74 or V_DDD = 75 or V_DDD = 77 or
                                V_DDD = 85 or V_DDD = 88 or
                                V_DDD = 61 or
                                V_DDD = 27 or V_DDD = 28 or
                                V_DDD = 62 or V_DDD = 64 or
                                V_DDD = 98 or V_DDD = 99 or
                                V_DDD = 31 or V_DDD = 32 or V_DDD = 33 or V_DDD = 34 or V_DDD = 35 or V_DDD = 37 or V_DDD = 38 or
                                V_DDD = 67 or
                                V_DDD = 65 or V_DDD = 66 or
                                V_DDD = 91 or V_DDD = 93 or V_DDD = 94 or
                                V_DDD = 83 or
                                V_DDD = 81 or V_DDD = 87 or
                                V_DDD = 86 or V_DDD = 89 or
                                V_DDD = 41 or V_DDD = 42 or V_DDD = 43 or V_DDD = 44 or V_DDD = 45 or V_DDD = 46 or
                                V_DDD = 21 or V_DDD = 22 or V_DDD = 24 or
                                V_DDD = 84 or
                                V_DDD = 69 or
                                V_DDD = 95 or
                                V_DDD = 51 or V_DDD = 53 or V_DDD = 55 or
                                V_DDD = 47 or V_DDD = 48 or V_DDD = 49 or
                                V_DDD = 79 or
                                V_DDD = 11 or V_DDD = 12 or V_DDD = 13 or V_DDD = 14 or V_DDD = 15 or V_DDD = 16 or V_DDD = 17 or V_DDD = 18 or V_DDD = 19 or
                                V_DDD = 63
                            THEN
                                SET V_ddd_errors = 0;
                            ELSE
                                UPDATE mailing_process SET id_send_sms = 9 WHERE id = V_ID;
                                SET V_fields_errors = CONCAT(V_fields_errors,'ddd|');
                                SET V_qtd_errors = V_qtd_errors + 1;
                            END IF;

                            IF LENGTH(V_PHONE) != 9 or LEFT(V_PHONE, 1) != 9  THEN
                                UPDATE mailing_process SET id_send_sms = 9 WHERE id = V_ID;
                                SET V_fields_errors = CONCAT(V_fields_errors,'telefone|');
                                SET V_qtd_errors = V_qtd_errors + 1;
                            END IF;
                            IF LENGTH(V_MESSAGE_SMS) > 160 THEN
                                SET V_fields_errors = CONCAT(V_fields_errors,'mensagem_sms|');
                                SET V_qtd_errors = V_qtd_errors + 1;
                            END IF;

                            IF V_JUST_SMS = 0 THEN
                                IF isValidDate(V_DATE_EVENT) = 0 THEN
                                    SET V_fields_errors = CONCAT(V_fields_errors,'data_inicio|');
                                    SET V_qtd_errors = V_qtd_errors + 1;
                                END IF;
                                IF LENGTH(V_TITLE) > 5 THEN
                                    SET V_fields_errors = CONCAT(V_fields_errors,'titulo_evento|');
                                    SET V_qtd_errors = V_qtd_errors + 1;
                                END IF;
                                IF LENGTH(V_DESCRIPTION) > 300 THEN
                                    SET V_fields_errors = CONCAT(V_fields_errors,'descricao|');
                                    SET V_qtd_errors = V_qtd_errors + 1;
                                END IF;
                                IF LENGTH(V_LOCATION) > 50 THEN
                                    SET V_fields_errors = CONCAT(V_fields_errors,'localizacao|');
                                    SET V_qtd_errors = V_qtd_errors + 1;
                                END IF;
                                IF LENGTH(V_IDENTIFICATION) > 50 THEN
                                    SET V_fields_errors = CONCAT(V_fields_errors,'identificador|');
                                    SET V_qtd_errors = V_qtd_errors + 1;
                                END IF;
                                IF LENGTH(V_JOKER_ONE) > 50 THEN
                                    SET V_fields_errors = CONCAT(V_fields_errors,'coringa_1|');
                                    SET V_qtd_errors = V_qtd_errors + 1;
                                END IF;
                                IF LENGTH(V_JOKER_TWO) > 50 THEN
                                    SET V_fields_errors = CONCAT(V_fields_errors,'coringa_2|');
                                    SET V_qtd_errors = V_qtd_errors + 1;
                                END IF;
                            END IF;

                            SET V_phone_blacklist = (SELECT COUNT(*) FROM blacklist WHERE ddd = V_DDD AND phone = V_PHONE AND id_client = V_ID_CLIENT AND active = 1 AND deleted_at IS NULL);
                            IF V_phone_blacklist > 0 THEN
                                UPDATE mailing_process SET id_send_sms = 16
                                WHERE id = V_ID;
                            END IF;

                            set V_TOTAL = V_TOTAL + 1;
                            IF V_qtd_errors > 0  THEN
                                INSERT INTO log_import_errors (id_client,id_campaigns,line_file,phone,name_file,qtd_errors,fields_errors,date_input,created_at)
                                VALUES (V_ID_CLIENT, V_ID_CAMPAIGN, V_TOTAL, V_DDD_PHONE, V_MAILING_FILE_GENION, V_qtd_errors, left(V_fields_errors, length(V_fields_errors)-1), NOW(), NOW());
                            END IF;

                            IF V_finished = 0 THEN
                                LEAVE get_mailing;
                            END IF;

                        END LOOP get_mailing;

                    CLOSE v_mailing;
                    select V_TOTAL;
                END;";
        DB::connection('mysql2')->unprepared($procedure);


        $dropFunction ="DROP FUNCTION IF EXISTS `isValidDate`;";
        DB::connection('mysql2')->unprepared($dropFunction);

        $function ="CREATE DEFINER=`root`@`localhost` FUNCTION `isValidDate`(actualDate varchar(255)) RETURNS int(11)
            begin
                declare flag int;
                if (select length(date(actualDate)) IS NOT NULL ) then
                    set flag = 1;
                else
                    set flag = 0;
                end if;
                return flag;
            end;";

        DB::connection('mysql2')->unprepared($function);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
