<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.products.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Product Name -->
                            <div class="md:col-span-2">
                                <x-input-label for="name" :value="__('Product Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Category -->
                            <div>
                                <x-input-label for="category_id" :value="__('Category')" />
                                <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>

                            <!-- Price -->
                            <div>
                                <x-input-label for="price" :value="__('Price (₹)')" />
                                <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" :value="old('price')" required />
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>

                            <!-- Stock Quantity -->
                            <div>
                                <x-input-label for="stock_quantity" :value="__('Stock Quantity')" />
                                <x-text-input id="stock_quantity" class="block mt-1 w-full" type="number" name="stock_quantity" :value="old('stock_quantity', 0)" required />
                                <x-input-error :messages="$errors->get('stock_quantity')" class="mt-2" />
                            </div>

                            <!-- Weight -->
                            <div>
                                <x-input-label for="weight" :value="__('Weight (kg)')" />
                                <x-text-input id="weight" class="block mt-1 w-full" type="number" step="0.01" name="weight" :value="old('weight')" />
                                <x-input-error :messages="$errors->get('weight')" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <!-- Materials -->
                            <div>
                                <x-input-label for="materials" :value="__('Materials')" />
                                <x-text-input id="materials" class="block mt-1 w-full" type="text" name="materials" :value="old('materials')" placeholder="e.g., Wood, Clay" />
                                <x-input-error :messages="$errors->get('materials')" class="mt-2" />
                            </div>

                            <!-- Dimensions -->
                            <div>
                                <x-input-label for="dimensions" :value="__('Dimensions')" />
                                <x-text-input id="dimensions" class="block mt-1 w-full" type="text" name="dimensions" :value="old('dimensions')" placeholder="e.g., 10cm x 15cm x 5cm" />
                                <x-input-error :messages="$errors->get('dimensions')" class="mt-2" />
                            </div>

                            <!-- Tags -->
                            <div class="md:col-span-2">
                                <x-input-label for="tags" :value="__('Tags')" />
                                <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2">
                                    @foreach($tags as $tag)
                                        <div class="flex items-center">
                                            <input type="checkbox" id="tag_{{ $tag->id }}" name="tags[]" value="{{ $tag->id }}"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                                            <label for="tag_{{ $tag->id }}" class="ml-2 text-sm text-gray-700">{{ $tag->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                            </div>

                            <!-- Is Featured -->
                            <div class="flex items-center">
                                <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    {{ old('is_featured') ? 'checked' : '' }}>
                                <label for="is_featured" class="ml-2 text-sm text-gray-700">Featured Product</label>
                            </div>

                            <!-- Is Active -->
                            <div class="flex items-center">
                                <input type="checkbox" id="is_active" name="is_active" value="1" checked
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active" class="ml-2 text-sm text-gray-700">Active (Visible on website)</label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-4">
                            <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
                            <x-primary-button>
                                {{ __('Create Product') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
