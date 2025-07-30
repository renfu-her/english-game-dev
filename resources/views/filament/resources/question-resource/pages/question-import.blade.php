<x-filament-panels::page>
    <x-filament-panels::form wire:submit="import">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit">
                匯入題目
            </x-filament::button>
        </div>
    </x-filament-panels::form>

    <div class="mt-8">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">CSV 檔案格式說明</h3>
        <div class="mt-4 bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                請確保您的 CSV 檔案包含以下欄位：
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">必填欄位：</h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li><strong>category_slug</strong> - 分類代碼（如：daily-conversation）</li>
                        <li><strong>type</strong> - 題目類型（choice 或 fill）</li>
                        <li><strong>question</strong> - 題目內容</li>
                        <li><strong>correct_answer</strong> - 正確答案</li>
                        <li><strong>options</strong> - 選項陣列（JSON 格式）</li>
                        <li><strong>difficulty</strong> - 難度（easy、medium、hard）</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">選填欄位：</h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li><strong>explanation</strong> - 解釋說明</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <h4 class="font-medium text-gray-900 dark:text-white mb-2">範例：</h4>
            <div class="bg-gray-900 text-gray-100 rounded-lg p-4 overflow-x-auto">
                <pre class="text-sm">category_slug,type,question,correct_answer,options,explanation,difficulty
daily-conversation,choice,"What's your name?","My name is John","['My name is John', 'I am fine', 'Thank you', 'Goodbye']","This is a common greeting question.",easy
travel-transport,fill,"I want to go to the ___ station.","airport","['airport', 'bus', 'train', 'subway', 'taxi']","Airport is the correct word for air travel.",medium</pre>
            </div>
        </div>
    </div>
</x-filament-panels::page>
