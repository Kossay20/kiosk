<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        // If the new columns already exist we assume the table is in the new format.
        if (Schema::hasColumn('orders', 'ordering_channel') && Schema::hasColumn('orders', 'payment_status')) {
            return;
        }

        if (Schema::hasColumn('orders', 'status')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        if (Schema::hasColumn('orders', 'payment_method')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('payment_method');
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'status')) {
                $table->string('status', 40)->default('pending_payment')->after('total_price');
            }
            if (! Schema::hasColumn('orders', 'ordering_channel')) {
                $table->string('ordering_channel', 40)->default('kiosk')->after('status');
            }
            if (! Schema::hasColumn('orders', 'payment_channel')) {
                $table->string('payment_channel', 40)->nullable()->after('ordering_channel');
            }
            if (! Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status', 40)->default('pending')->after('payment_channel');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('orders', 'payment_channel')) {
                $table->dropColumn('payment_channel');
            }
            if (Schema::hasColumn('orders', 'ordering_channel')) {
                $table->dropColumn('ordering_channel');
            }
            if (Schema::hasColumn('orders', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'status')) {
                $table->enum('status', ['pending', 'paid', 'cooking', 'done'])->default('pending')->after('total_price');
            }
            if (! Schema::hasColumn('orders', 'payment_method')) {
                $table->enum('payment_method', ['kiosk', 'cashier'])->nullable()->after('status');
            }
        });
    }
};
