    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropForeign(['usuario_apertura_id']);
            $table->dropForeign(['usuario_cierre_id']);
        });

        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn([
                'monto_apertura',
                'fecha_apertura',
                'fecha_cierre',
                'monto_cierre_esperado',
                'monto_cierre_real',
                'diferencia',
                'usuario_apertura_id',
                'usuario_cierre_id',
            ]);
            $table->foreignId('almacen_id')->nullable()->after('sucursal_id')
                ->constrained('almacenes')->nullOnDelete();
        });

        Schema::table('cajas', function (Blueprint $table) {
            $table->enum('estado', ['activa', 'inactiva'])->default('activa')->change();
        });

        Schema::create('sesiones_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas')->cascadeOnDelete();
            $table->decimal('monto_apertura', 12, 2)->default(0);
            $table->datetime('fecha_apertura');
            $table->datetime('fecha_cierre')->nullable();
            $table->decimal('monto_cierre_esperado', 12, 2)->default(0);
            $table->decimal('monto_cierre_real', 12, 2)->nullable();
            $table->decimal('diferencia', 12, 2)->nullable();
            $table->text('observacion_diferencia')->nullable();
            $table->foreignId('usuario_apertura_id')->constrained('users');
            $table->foreignId('usuario_cierre_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->timestamps();
        });
    }

        public function down(): void
    {
        Schema::dropIfExists('sesiones_caja');

        Schema::table('cajas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('almacen_id');
            $table->decimal('monto_apertura', 12, 2)->default(0);
            $table->datetime('fecha_apertura')->nullable();
            $table->datetime('fecha_cierre')->nullable();
            $table->decimal('monto_cierre_esperado', 12, 2)->default(0);
            $table->decimal('monto_cierre_real', 12, 2)->default(0);
            $table->decimal('diferencia', 12, 2)->default(0);
            $table->foreignId('usuario_apertura_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('usuario_cierre_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }
    };