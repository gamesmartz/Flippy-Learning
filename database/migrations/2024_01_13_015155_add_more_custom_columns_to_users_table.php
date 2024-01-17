<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreCustomColumnsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('user_registered_time')->nullable();
            $table->tinyInteger('user_role')->default(2);
            $table->tinyInteger('user_status')->default(1);
            $table->tinyInteger('subscription')->default(1);
            $table->string('que', 255)->nullable();
            $table->integer('level')->default(1);
            $table->integer('points')->default(0);
            $table->integer('total_points')->default(0);
            $table->integer('total_answered')->default(0);
            $table->integer('check_answered')->default(0);
            $table->integer('check_time')->default(0);
            $table->tinyInteger('check_flag')->default(0);
            $table->dateTime('study_time')->nullable();
            $table->integer('professor_points')->default(0);
            $table->dateTime('login_time')->nullable();
            $table->string('reports_option', 255)->nullable();
            $table->string('reports_email', 255)->nullable();
            $table->string('reports_phone', 255)->nullable();
            $table->string('country_code', 255)->nullable();
            $table->string('reports_time', 255)->nullable();
            $table->string('reports_interval', 255)->nullable();
            $table->string('choose_history', 255)->nullable();
            $table->tinyInteger('mastered_num')->default(0);
            $table->string('admin_history', 255)->nullable();
            $table->string('video_history', 255)->nullable();
            $table->string('watch_history', 255)->nullable();
            $table->string('track_history', 255)->default('30');
            $table->string('beta_key', 255)->nullable();
            $table->tinyInteger('audio')->default(1);
            $table->tinyInteger('zoom')->default(0);
            $table->tinyInteger('show_leader')->default(1);
            $table->string('last_exe_played', 255)->nullable();
            $table->string('time_remaining', 255)->nullable();
            $table->tinyInteger('new_mastered')->default(0);
            $table->integer('current_grade_level')->default(0);
            $table->string('vocab_search_grade', 255)->nullable();
            $table->integer('user_level')->default(1);
            $table->string('nickname', 255)->nullable();            
            $table->string('user_ip_v4', 255)->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'user_ip_v4',
                'nickname',
                'user_level',
                'vocab_search_grade',
                'current_grade_level',
                'new_mastered',
                'time_remaining',
                'last_exe_played',
                'show_leader',
                'zoom',
                'audio',
                'beta_key',
                'track_history',
                'watch_history',
                'video_history',
                'admin_history',
                'mastered_num',
                'choose_history',
                'reports_interval',
                'reports_time',
                'country_code',
                'reports_phone',
                'reports_email',
                'reports_option',
                'login_time',
                'professor_points',
                'study_time',
                'check_flag',
                'check_time',
                'check_answered',
                'total_answered',
                'total_points',
                'points',
                'level',
                'que',
                'subscription',
                'user_status',
                'user_role',
                'user_registered_time'
            ]);
        });
    }
}
