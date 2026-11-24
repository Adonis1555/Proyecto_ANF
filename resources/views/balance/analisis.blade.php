@extends('layouts.app')

@section('title', 'Análisis Balance General')

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal y Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-cyan-600 transition">Inicio</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <a href="{{ route('balance.index') }}" class="hover:text-cyan-600 transition">Listado de Balance General</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-semibold">Análisis Comparativo</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 text-cyan-600 border border-cyan-500/20 flex items-center justify-center font-bold">
                    <i class="bi bi-bar-chart-line-fill text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Análisis de Balance General</h1>
                    <p class="text-xs text-slate-500">
                        Evaluación horizontal y vertical del periodo
                        <span class="font-bold text-slate-700">{{ $anio_menor }}</span> (Año Base) al
                        <span class="font-bold text-slate-700">{{ $anio_mayor }}</span> (Año a Comparar)
                    </p>
                </div>
            </div>
        </div>

        <a href="{{ route('balance.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition self-start sm:self-auto">
            <i class="bi bi-arrow-left text-sm"></i>
            Volver a Balances
        </a>
    </div>

    <!-- Tarjetas de Resumen Rápido -->
    @if(!empty($totalesBalance))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $cards = [
                ['titulo' => 'Total Activos (Base 100%)', 'data' => $totalesBalance['activos'], 'accent' => 'border-sky-200 bg-sky-50/40 text-sky-900'],
                ['titulo' => 'Total Pasivos', 'data' => $totalesBalance['pasivos'], 'accent' => 'border-amber-200 bg-amber-50/40 text-amber-900'],
                ['titulo' => 'Total Patrimonio', 'data' => $totalesBalance['patrimonio'], 'accent' => 'border-indigo-200 bg-indigo-50/40 text-indigo-900'],
                ['titulo' => 'Pasivo + Patrimonio (Base 100%)', 'data' => $totalesBalance['pasivo_patrimonio'], 'accent' => 'border-cyan-200 bg-cyan-50/40 text-cyan-900'],
            ];
        @endphp

        @foreach($cards as $card)
            <div class="p-4 rounded-2xl border {{ $card['accent'] }} shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider opacity-80">{{ $card['titulo'] }}</span>
                    <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-white/80 border border-slate-200">
                        {{ number_format($card['data']['vertical_mayor'], 2) }}%
                    </span>
                </div>
                <div class="flex items-baseline justify-between pt-1">
                    <div>
                        <p class="text-[11px] text-slate-500">{{ $anio_mayor }}</p>
                        <p class="text-lg font-mono font-bold">$ {{ number_format($card['data']['mayor'], 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] text-slate-500">Base {{ $anio_menor }}</p>
                        <p class="text-xs font-mono font-semibold text-slate-600">$ {{ number_format($card['data']['menor'], 2) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Tabla del Análisis Financiero -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[960px]">
                <thead>
                    <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider border-b border-slate-800">
                        <th rowspan="2" class="py-3.5 px-6 font-semibold align-middle w-4/12 border-r border-slate-800/60">Cuenta Contable</th>
                        <th colspan="2" class="py-2.5 px-4 font-semibold text-center border-r border-slate-800/60 bg-slate-800/40">Saldos Registrados</th>
                        <th colspan="2" class="py-2.5 px-4 font-semibold text-center border-r border-slate-800/60 bg-cyan-950/40 text-cyan-300">Análisis Horizontal</th>
                        <th colspan="2" class="py-2.5 px-4 font-semibold text-center bg-slate-800/40">Análisis Vertical</th>
                    </tr>
                    <tr class="bg-slate-900 text-slate-300 text-[11px] uppercase tracking-wider border-b border-slate-800">
                        <th class="py-2.5 px-4 font-semibold text-right border-r border-slate-800/60">{{ $anio_menor }} (Base)</th>
                        <th class="py-2.5 px-4 font-semibold text-right border-r border-slate-800/60">{{ $anio_mayor }}</th>
                        <th class="py-2.5 px-4 font-semibold text-right border-r border-slate-800/60 text-cyan-400">Var. Absoluta ($)</th>
                        <th class="py-2.5 px-4 font-semibold text-center border-r border-slate-800/60 text-cyan-400">Var. Relativa (%)</th>
                        <th class="py-2.5 px-4 font-semibold text-center border-r border-slate-800/60">{{ $anio_menor }} (%)</th>
                        <th class="py-2.5 px-4 font-semibold text-center">{{ $anio_mayor }} (%)</th>
                    </tr>
                </thead>

                @php
                    $secciones = [
                        'activo' => [
                            'titulo'      => '1. ACTIVOS',
                            'total_label' => 'TOTAL ACTIVOS (BASE 100%)',
                            'total_key'   => 'activos',
                            'header_bg'   => 'bg-sky-50 text-sky-900 border-sky-200',
                            'total_bg'    => 'bg-sky-900 text-white',
                        ],
                        'pasivo' => [
                            'titulo'      => '2. PASIVOS',
                            'total_label' => 'TOTAL PASIVOS',
                            'total_key'   => 'pasivos',
                            'header_bg'   => 'bg-amber-50 text-amber-900 border-amber-200',
                            'total_bg'    => 'bg-slate-800 text-white',
                        ],
                        'patrimonio' => [
                            'titulo'      => '3. PATRIMONIO',
                            'total_label' => 'TOTAL PATRIMONIO',
                            'total_key'   => 'patrimonio',
                            'header_bg'   => 'bg-indigo-50 text-indigo-900 border-indigo-200',
                            'total_bg'    => 'bg-slate-800 text-white',
                        ],
                    ];
                @endphp

                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($secciones as $claveGrupo => $sec)
                        @php
                            $filasGrupo = $gruposCuentas[$claveGrupo] ?? [];
                            $totalFila  = $totalesBalance[$sec['total_key']] ?? null;
                        @endphp

                        @if(count($filasGrupo) > 0)
                            <tr class="{{ $sec['header_bg'] }} border-y">
                                <td colspan="7" class="py-2.5 px-6 text-xs font-extrabold uppercase tracking-wider">
                                    {{ $sec['titulo'] }}
                                </td>
                            </tr>

                            @foreach($filasGrupo as $item)
                                @php $esAcumuladora = isset($item['es_detalle']) && !$item['es_detalle']; @endphp
                                <tr class="{{ $esAcumuladora ? 'bg-slate-50/90 font-semibold' : 'hover:bg-slate-50/80' }} transition">
                                    <td class="py-3 px-6 text-slate-800 border-r border-slate-100">
                                        <div class="flex items-center gap-2.5">
                                            @if(!empty($item['codigo']))
                                                <span class="font-mono text-xs text-cyan-700 bg-cyan-50 px-2 py-0.5 rounded border border-cyan-100">
                                                    {{ $item['codigo'] }}
                                                </span>
                                            @endif
                                            <span class="{{ $esAcumuladora ? 'text-slate-900 font-bold' : 'font-medium text-slate-800' }}">
                                                {{ $item['cuenta'] }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono text-slate-600 border-r border-slate-100 whitespace-nowrap">
                                        $ {{ number_format($item['saldo_menor'], 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-semibold text-slate-900 border-r border-slate-100 whitespace-nowrap">
                                        $ {{ number_format($item['saldo_mayor'], 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-semibold border-r border-slate-100 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 {{ $item['cambio'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                            <i class="bi {{ $item['cambio'] >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }} text-xs"></i>
                                            $ {{ number_format($item['cambio'], 2) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center border-r border-slate-100 whitespace-nowrap">
                                        @if(!is_null($item['porcentaje_cambio']))
                                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-xs font-mono font-bold border {{ $item['porcentaje_cambio'] >= 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200/60' : 'bg-rose-50 text-rose-700 border-rose-200/60' }}">
                                                {{ number_format($item['porcentaje_cambio'], 2) }} %
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-xs font-mono font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                                N/A
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono text-slate-600 border-r border-slate-100 whitespace-nowrap">
                                        {{ !is_null($item['vertical_menor']) ? number_format($item['vertical_menor'], 2) . ' %' : '—' }}
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono text-slate-700 font-medium whitespace-nowrap">
                                        {{ !is_null($item['vertical_mayor']) ? number_format($item['vertical_mayor'], 2) . ' %' : '—' }}
                                    </td>
                                </tr>
                            @endforeach

                            @if($totalFila)
                                <tr class="{{ $sec['total_bg'] }} font-mono text-xs font-bold">
                                    <td class="py-3 px-6 font-sans uppercase tracking-wider border-r border-white/10">
                                        {{ $sec['total_label'] }}
                                    </td>
                                    <td class="py-3 px-4 text-right border-r border-white/10 whitespace-nowrap">
                                        $ {{ number_format($totalFila['menor'], 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-right border-r border-white/10 whitespace-nowrap">
                                        $ {{ number_format($totalFila['mayor'], 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-right border-r border-white/10 text-cyan-300 whitespace-nowrap">
                                        $ {{ number_format($totalFila['cambio'], 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-center border-r border-white/10 text-cyan-300 whitespace-nowrap">
                                        {{ !is_null($totalFila['var_relativa']) ? number_format($totalFila['var_relativa'], 2) . ' %' : 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4 text-center border-r border-white/10 text-cyan-300 whitespace-nowrap">
                                        {{ number_format($totalFila['vertical_menor'], 2) }} %
                                    </td>
                                    <td class="py-3 px-4 text-center text-cyan-300 whitespace-nowrap">
                                        {{ number_format($totalFila['vertical_mayor'], 2) }} %
                                    </td>
                                </tr>
                            @endif
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <p class="text-sm font-medium text-slate-500">¡No existe Balance General registrado para los años seleccionados!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if(!empty($totalesBalance['pasivo_patrimonio']))
                    @php $pp = $totalesBalance['pasivo_patrimonio']; @endphp
                    <tfoot class="bg-cyan-950 text-white font-mono text-xs font-bold border-t-2 border-cyan-500">
                        <tr>
                            <td class="py-3.5 px-6 font-sans uppercase tracking-wider border-r border-white/10">
                                TOTAL PASIVO + PATRIMONIO (BASE 100%)
                            </td>
                            <td class="py-3.5 px-4 text-right border-r border-white/10 whitespace-nowrap">
                                $ {{ number_format($pp['menor'], 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right border-r border-white/10 whitespace-nowrap">
                                $ {{ number_format($pp['mayor'], 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right border-r border-white/10 text-cyan-300 whitespace-nowrap">
                                $ {{ number_format($pp['cambio'], 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-center border-r border-white/10 text-cyan-300 whitespace-nowrap">
                                {{ !is_null($pp['var_relativa']) ? number_format($pp['var_relativa'], 2) . ' %' : 'N/A' }}
                            </td>
                            <td class="py-3.5 px-4 text-center border-r border-white/10 text-cyan-300 whitespace-nowrap">
                                {{ number_format($pp['vertical_menor'], 2) }} %
                            </td>
                            <td class="py-3.5 px-4 text-center text-cyan-300 whitespace-nowrap">
                                {{ number_format($pp['vertical_mayor'], 2) }} %
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>
@endsection