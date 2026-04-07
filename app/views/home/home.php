<?php $this->layout("template") ?>
<section class="w-full mx-auto mt-8 bg-white shadow-md rounded-lg p-6 border border-gray-200">
    <div class="m-auto max-w-[60rem] w-full flex-1 flex items-start gap-4 text-zinc-800">
        <aside class="pt-16 flex-shrink-0">
            <h3 class="font-bold text-xl mb-4">CONCURSO:</h3>

            <ul class="flex flex-col gap-1">
                <li>
                    <a href="#"
                        class="block p-2 rounded-md whitespace-nowrap font-semibold transition-colors duration-300 text-gray-700 hover:text-gray-50 hover:bg-green-800">
                        Inscrições Abertas
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="block p-2 rounded-md whitespace-nowrap font-semibold transition-colors duration-300 text-gray-700 hover:text-gray-50 hover:bg-green-800">
                        Andamento
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="block p-2 rounded-md whitespace-nowrap font-semibold transition-colors duration-300 text-gray-700 hover:text-gray-50 hover:bg-green-800">
                        Finalizados
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="block p-2 rounded-md whitespace-nowrap font-semibold transition-colors duration-300 text-gray-700 hover:text-gray-50 hover:bg-green-800">
                        Recurso Aberto
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="block p-2 rounded-md whitespace-nowrap font-semibold transition-colors duration-300 text-gray-700 hover:text-gray-50 hover:bg-green-800">
                        Cancelados
                    </a>
                </li>
            </ul>
        </aside>

        <div class="bg-white min-h-[46rem] w-full mt-8 shadow-md rounded overflow-hidden">
            <h2
                class="text-3xl uppercase py-8 px-5 border-b border-b-gray-300 border-t-[6px] border-t-green-900 shadow-sm">
                Concurso -
                <span class="font-bold">Inscrições Abertas</span>
            </h2>


            <a href="/login" class="block p-10 border-b border-b-gray-300">
                <div class="flex items-start gap-5">
                    <div class="h-24 w-40 flex items-center justify-center border border-gray-300 rounded shadow-sm">
                        <img src="/assets/brasao.png" alt="">
                    </div>
                    <div class="flex flex-col gap-1 w-full">
                        <p class="text-sm">Processo Seletivo</p>
                        <span class="font-bold text-xl">
                            Processo Seletivo - EDITAL N.º 001/2024 – 5º PSS/PMP - Prefeitura Municipal de Parauapebas
                        </span>
                        <p class="text-xs">
                            Edital nº EDITAL N.º 03601/2024 – 5º PSS/PMP
                            <br />
                            Inscrições de 23/04/2024 a 29/04/2024
                        </p>
                    </div>
                    <div class="flex flex-col justify-center items-center gap-4">
                        <div class="flex flex-col justify-center items-center">
                            <span class="text-5xl font-bold">607</span>
                            <p class="uppercase text-xs w-36 text-center">Quantidade de vagas</p>
                        </div>
                        <button type="button"
                            class="inline-block cursor-pointer align-middle rounded-[3px] border border-[#d6d6d6] bg-white px-[10px] pt-[5px] pb-[7px] text-xs font-bold uppercase text-[#666666] no-underline shadow-[inset_0_-2px_1px_0_#ebebeb] leading-[18px]">
                            Mais Informações
                        </button>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>


