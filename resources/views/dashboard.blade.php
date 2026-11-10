@extends('layouts.app')

@section('title', 'Panel Principal')

@section('content')
<div class="space-y-8">

    <!-- Hero / Banner de Bienvenida -->
    <div class="relative overflow-hidden rounded-2xl bg-slate-900 text-white p-8 border border-slate-800 shadow-xl">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 max-w-2xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-semibold">
                <i class="bi bi-shield-check"></i>
                Sistema de Gestión Financiera
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                Bienvenido a <span class="text-cyan-400">EconomyManager</span>
            </h1>
            <p class="text-sm text-slate-300 leading-relaxed">
                Plataforma ejecutiva para el análisis de estados financieros, cálculo automático de ratios y gestión masiva de estructuras contables.
            </p>
        </div>
    </div>

    <!-- Módulos Principales (Grid de Accesos Rápidos) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Módulo 1: Catálogo de Cuentas -->
        <a href="{{ route('catalogo.index') }}" class="group bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-cyan-500/40 transition flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="h-12 w-12 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold text-xl group-hover:bg-cyan-600 group-hover:text-white transition">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base group-hover:text-cyan-600 transition">Catálogo de Cuentas</h3>
                    <p class="text-xs text-slate-500 mt-1">Estructura contable codificada y carga masiva de ítems.</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 text-xs font-semibold text-cyan-600">
                <span>Acceder al catálogo</span>
                <i class="bi bi-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Módulo 2: Balance General -->
        <a href="{{ route('balance.index') }}" class="group bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-cyan-500/40 transition flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="h-12 w-12 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold text-xl group-hover:bg-cyan-600 group-hover:text-white transition">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base group-hover:text-cyan-600 transition">Balance General</h3>
                    <p class="text-xs text-slate-500 mt-1">Situación patrimonial, saldos por año y análisis comparativo.</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 text-xs font-semibold text-cyan-600">
                <span>Ver Balances</span>
                <i class="bi bi-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Módulo 3: Estado de Resultados -->
        <a href="{{ route('estado-resultados.index') }}" class="group bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-cyan-500/40 transition flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="h-12 w-12 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold text-xl group-hover:bg-cyan-600 group-hover:text-white transition">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base group-hover:text-cyan-600 transition">Estado de Resultados</h3>
                    <p class="text-xs text-slate-500 mt-1">Reportes de pérdidas, ganancias e ingresos del ejercicio.</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 text-xs font-semibold text-cyan-600">
                <span>Ver Estados</span>
                <i class="bi bi-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Módulo 4: Ratios Financieros -->
        <a href="{{ route('ratios.index') }}" class="group bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-cyan-500/40 transition flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="h-12 w-12 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold text-xl group-hover:bg-cyan-600 group-hover:text-white transition">
                    <i class="bi bi-calculator-fill"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base group-hover:text-cyan-600 transition">Ratios Financieros</h3>
                    <p class="text-xs text-slate-500 mt-1">Liquidez, endeudamiento, rentabilidad y actividad.</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 text-xs font-semibold text-cyan-600">
                <span>Calcular Ratios</span>
                <i class="bi bi-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

    </div>

</div>
@endsection