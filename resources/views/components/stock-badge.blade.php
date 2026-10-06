@props(['product'])

<x-badge :color="$product->stock_color">{{ $product->stock_label }}</x-badge>