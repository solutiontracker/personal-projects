<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfBadgesPrinterQueueTable extends Migration
{
    const TABLE = 'conf_badges_printer_queue';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('event_id')->index('event_id');
            $table->string('type')->nullable()->index('type');
            $table->integer('attendee_type')->index('attendee_type');
            $table->integer('badge_id')->index('badge_id');
            $table->text('name');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('companyName')->nullable();
            $table->text('title');
            $table->text('companyAddress');
            $table->text('privateAddress');
            $table->string('telephone', 100);
            $table->string('companyAddress_1');
            $table->string('privateAddress_1');
            $table->string('telephone_1');
            $table->text('mobile')->nullable();
            $table->text('mobile_2')->comment('Use for drop down 2');
            $table->text('mobile_3')->comment('Use for drop down 3');
            $table->text('mobile_4')->comment('Use for drop down 4');
            $table->text('mobile_5')->comment('Use for drop down 5');
            $table->text('mobile_6')->comment('Use for drop down 6');
            $table->text('mobile_7')->comment('Use for drop down 7');
            $table->text('mobile_8')->comment('Use for drop down 8');
            $table->text('mobile_9')->comment('Use for drop down 9');
            $table->text('mobile_10')->comment('Use for drop down 10');
            $table->text('interests');
            $table->text('logo');
            $table->text('image');
            $table->text('bg_image');
            $table->text('textfield');
            $table->string('email');
            $table->string('productArea');
            $table->text('department');
            $table->text('barcode');
            $table->text('country');
            $table->text('organization');
            $table->text('delegateNumber');
            $table->string('tableNumber');
            $table->text('networkGroup');
            $table->string('printer')->comment('This column will be used to save printer information where card need to print.');
            $table->string('printer_group')->nullable()->index('printer_group')->comment('This column will be used to save printer group information where card need to print.');
            $table->tinyInteger('printed')->default(0)->index('printed');
            $table->dateTime('printed_at');
            $table->text('name_1');
            $table->string('firstname_1');
            $table->string('lastname_1');
            $table->text('companyName_1');
            $table->text('title_1');
            $table->text('barcode_1');
            $table->text('jobTask');
            $table->text('initial');
            $table->text('age');
            $table->text('gender');
            $table->text('birthDate');
            $table->text('department1');
            $table->text('employmentDate');
            $table->text('industry');
            $table->text('about');
            $table->text('attendeeGroups');
            $table->text('attendeeType');
            $table->string('jobTask_1');
            $table->string('initial_1');
            $table->string('age_1');
            $table->string('gender_1');
            $table->string('birthDate_1');
            $table->string('department1_1');
            $table->string('employmentDate_1');
            $table->string('industry_1');
            $table->string('about_1');
            $table->string('attendeeGroups_1');
            $table->string('attendeeType_1');
            $table->string('textfield_1');
            $table->string('email_1');
            $table->string('productArea_1');
            $table->string('department_1');
            $table->string('country_1');
            $table->string('organization_1');
            $table->string('delegateNumber_1');
            $table->string('tableNumber_1', 254);
            $table->string('networkGroup_1', 254);
            $table->text('firstNamePassport');
            $table->text('firstNamePassport_1');
            $table->text('lastNamePassport');
            $table->text('lastNamePassport_1');
            $table->text('placeBirthPassport');
            $table->text('placeBirthPassport_1');
            $table->text('passportNo');
            $table->text('passportNo_1');
            $table->text('dateIssuePassport');
            $table->text('dateIssuePassport_1');
            $table->text('dateExpiryPassport');
            $table->text('dateExpiryPassport_1');
            $table->text('privateHouseNo');
            $table->text('privateHouseNo_1');
            $table->text('privateStreet');
            $table->text('privateStreet_1');
            $table->text('privatePostCode');
            $table->text('privatePostCode_1');
            $table->text('privateCity');
            $table->text('privateCity_1');
            $table->text('privateCountry');
            $table->text('privateCountry_1');
            $table->text('interests_1');
            $table->timestamps();
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->string('type')->nullable()->index('type');
                $table->integer('attendee_type')->index('attendee_type');
                $table->integer('badge_id')->index('badge_id');
                $table->text('name');
                $table->string('firstname');
                $table->string('lastname');
                $table->string('companyName')->nullable();
                $table->text('title');
                $table->text('companyAddress');
                $table->text('privateAddress');
                $table->string('telephone', 100);
                $table->string('companyAddress_1');
                $table->string('privateAddress_1');
                $table->string('telephone_1');
                $table->text('mobile')->nullable();
                $table->text('mobile_2')->comment('Use for drop down 2');
                $table->text('mobile_3')->comment('Use for drop down 3');
                $table->text('mobile_4')->comment('Use for drop down 4');
                $table->text('mobile_5')->comment('Use for drop down 5');
                $table->text('mobile_6')->comment('Use for drop down 6');
                $table->text('mobile_7')->comment('Use for drop down 7');
                $table->text('mobile_8')->comment('Use for drop down 8');
                $table->text('mobile_9')->comment('Use for drop down 9');
                $table->text('mobile_10')->comment('Use for drop down 10');
                $table->text('interests');
                $table->text('logo');
                $table->text('image');
                $table->text('bg_image');
                $table->text('textfield');
                $table->string('email');
                $table->string('productArea');
                $table->text('department');
                $table->text('barcode');
                $table->text('country');
                $table->text('organization');
                $table->text('delegateNumber');
                $table->string('tableNumber');
                $table->text('networkGroup');
                $table->string('printer')->comment('This column will be used to save printer information where card need to print.');
                $table->string('printer_group')->nullable()->index('printer_group')->comment('This column will be used to save printer group information where card need to print.');
                $table->tinyInteger('printed')->default(0)->index('printed');
                $table->dateTime('printed_at');
                $table->text('name_1');
                $table->string('firstname_1');
                $table->string('lastname_1');
                $table->text('companyName_1');
                $table->text('title_1');
                $table->text('barcode_1');
                $table->text('jobTask');
                $table->text('initial');
                $table->text('age');
                $table->text('gender');
                $table->text('birthDate');
                $table->text('department1');
                $table->text('employmentDate');
                $table->text('industry');
                $table->text('about');
                $table->text('attendeeGroups');
                $table->text('attendeeType');
                $table->string('jobTask_1');
                $table->string('initial_1');
                $table->string('age_1');
                $table->string('gender_1');
                $table->string('birthDate_1');
                $table->string('department1_1');
                $table->string('employmentDate_1');
                $table->string('industry_1');
                $table->string('about_1');
                $table->string('attendeeGroups_1');
                $table->string('attendeeType_1');
                $table->string('textfield_1');
                $table->string('email_1');
                $table->string('productArea_1');
                $table->string('department_1');
                $table->string('country_1');
                $table->string('organization_1');
                $table->string('delegateNumber_1');
                $table->string('tableNumber_1', 254);
                $table->string('networkGroup_1', 254);
                $table->text('firstNamePassport');
                $table->text('firstNamePassport_1');
                $table->text('lastNamePassport');
                $table->text('lastNamePassport_1');
                $table->text('placeBirthPassport');
                $table->text('placeBirthPassport_1');
                $table->text('passportNo');
                $table->text('passportNo_1');
                $table->text('dateIssuePassport');
                $table->text('dateIssuePassport_1');
                $table->text('dateExpiryPassport');
                $table->text('dateExpiryPassport_1');
                $table->text('privateHouseNo');
                $table->text('privateHouseNo_1');
                $table->text('privateStreet');
                $table->text('privateStreet_1');
                $table->text('privatePostCode');
                $table->text('privatePostCode_1');
                $table->text('privateCity');
                $table->text('privateCity_1');
                $table->text('privateCountry');
                $table->text('privateCountry_1');
                $table->text('interests_1');
                $table->timestamps();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        EBSchema::dropDeleteTrigger(self::TABLE);
        Schema::dropIfExists(self::TABLE);
            Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE);
    }
}
