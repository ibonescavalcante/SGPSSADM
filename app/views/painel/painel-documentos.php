 <?php $this->layout('/painel/painel-template')
    ?>
 <div class="bg-gray-100 flex-1 flex flex-col items-center text-zinc-800">
     <div class="flex flex-col gap-6 bg-white min-h-[46rem] w-[54vw] m-8 shadow-md rounded-b-2xl">
         <h2
             class="text-3xl uppercase py-8 px-5 border-b border-b-gray-300 border-t-[6px] border-t-green-900 shadow-sm">
             Área do Candidato -
             <span class="font-bold">Documentos</span>
         </h2>

         <div class="px-5">
             <table class="w-full border-collapse text-sm">
                 <thead class="bg-gray-100">
                     <tr>
                         <th class="p-1 text-base text-center border border-gray-300">
                             Tipo
                         </th>
                         <th class="p-1 text-base text-center border border-gray-300">
                             Documento
                         </th>
                         <th class="p-1 text-base text-center border border-gray-300">
                             Enviado em
                         </th>
                         <th class="p-1 text-base text-center border border-gray-300">
                             Ação
                         </th>
                     </tr>
                 </thead>
                 <tbody>
                     <tr class="bg-white hover:bg-gray-50">
                         <td class="p-3 text-base text-center border border-gray-300">
                             <a href="#" class="text-blue-600 hover:underline">
                                 Huistorico escolar
                             </a>
                         </td>
                         <td class="p-3 text-base text-center border border-gray-300">
                             INFORMÁTICA - <br /> Conceição do Araguaia
                         </td>
                         <td class="p-3 text-base text-center border border-gray-300">
                             30/06/2025 11:50
                         </td>
                         <td class="p-3 text-base text-center border border-gray-300">
                             <a href="#" class="text-blue-600 hover:underline"> Editar | Excluir </a>
                         </td>
                     </tr>
                     <tr class="bg-white hover:bg-gray-50">
                         <td class="p-3 text-base text-center border border-gray-300">
                             <a href="#" class="text-blue-600 hover:underline">
                                 Huistorico escolar
                             </a>
                         </td>
                         <td class="p-3 text-base text-center border border-gray-300">
                             INFORMÁTICA - <br /> Conceição do Araguaia
                         </td>
                         <td class="p-3 text-base text-center border border-gray-300">
                             30/06/2025 11:50
                         </td>
                         <td class="p-3 text-base text-center border border-gray-300">
                             <a href="#" class="text-blue-600 hover:underline"> Editar | Excluir </a>
                         </td>
                     </tr>
                     <tr class="bg-white hover:bg-gray-50">
                         <td class="p-3 text-base text-center border border-gray-300">
                             <a href="#" class="text-blue-600 hover:underline">
                                 Huistorico escolar
                             </a>
                         </td>
                         <td class="p-3 text-base text-center border border-gray-300">
                             INFORMÁTICA - <br /> Conceição do Araguaia
                         </td>
                         <td class="p-3 text-base text-center border border-gray-300">
                             30/06/2025 11:50
                         </td>
                         <td class="p-3 text-base text-center border border-gray-300">
                             <a href="#" class="text-blue-600 hover:underline"> Editar | Excluir </a>
                         </td>
                     </tr>
                     <tr>
                         <td colspan="4" class="p-3 text-base text-center border border-gray-300">
                             Nenhum documento cadastrado.
                         </td>
                     </tr>
                 </tbody>
             </table>
         </div>
         <div
             style="background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; ">
             <h2 style="text-align: center; color: #333;">Envio de Documentos</h2>
             <form action="processa_documento.php" method="POST" enctype="multipart/form-data">
                 <label for="tipo_documento"
                     style="display: block; margin-top: 15px; font-weight: bold; color: #444;">Tipo de
                     documento:</label>
                 <select id="tipo_documento" name="tipo_documento" required
                     style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box;">
                     <option value="">Selecione</option>
                     <option value="1">Doc01</option>
                     <option value="2">Doc02</option>
                     <option value="3">Doc03/option>
                     <option value="outro">Outro</option>
                 </select>

                 <label for="documento"
                     style="display: block; margin-top: 15px; font-weight: bold; color: #444;">Selecionar
                     arquivo:</label>
                 <input type="file" id="documento" name="documento" accept=".pdf,.jpg,.jpeg,.png" required
                     style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box;">

                 <button type="submit"
                     style="margin-top: 20px; width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 6px; font-size: 16px; cursor: pointer;"
                     onmouseover="this.style.backgroundColor='#0056b3';"
                     onmouseout="this.style.backgroundColor='#007bff';">
                     Enviar Documento
                 </button>
             </form>
         </div>
         <div class="px-5 py-11 flex justify-end items-center">
             <a href="javascript:history.back()"
                 class="inline-block cursor-pointer align-middle rounded-[3px] border border-[#d6d6d6] bg-white px-[10px] pt-[5px] pb-[7px] text-xs font-bold uppercase text-[#666666] no-underline shadow-[inset_0_-2px_1px_0_#ebebeb] leading-[18px]">
                 Voltar
             </a>
         </div>
     </div>
 </div>