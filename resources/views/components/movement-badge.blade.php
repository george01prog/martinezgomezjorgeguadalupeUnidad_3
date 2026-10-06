@props(['movement'])

<x-badge :color="$movement->type_color">{{ $movement->type_label }}</x-badge>