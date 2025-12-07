<?php
/**
 * UI Helper - Design System
 * Dental MX
 * 
 * Funciones helper para generar componentes del Design System rápidamente.
 * 
 * Uso: Cargar en BaseController o autoload
 * helper('ui');
 */

if (!function_exists('ds_btn')) {
    /**
     * Genera un botón del Design System
     * 
     * @param string $text Texto del botón
     * @param string $variant Variante: primary, secondary, success, danger, warning, info, outline-primary, etc.
     * @param array $attrs Atributos adicionales: href, type, class, id, onclick, disabled, size, icon
     * @return string HTML del botón
     */
    function ds_btn(string $text, string $variant = 'primary', array $attrs = []): string
    {
        $tag = isset($attrs['href']) ? 'a' : 'button';
        $classes = ['ds-btn', "ds-btn--{$variant}"];
        
        // Tamaño
        if (isset($attrs['size'])) {
            $classes[] = "ds-btn--{$attrs['size']}";
            unset($attrs['size']);
        }
        
        // Block
        if (isset($attrs['block']) && $attrs['block']) {
            $classes[] = 'ds-btn--block';
            unset($attrs['block']);
        }
        
        // Clases adicionales
        if (isset($attrs['class'])) {
            $classes[] = $attrs['class'];
            unset($attrs['class']);
        }
        
        // Icono
        $iconHtml = '';
        if (isset($attrs['icon'])) {
            $iconPosition = $attrs['iconPosition'] ?? 'left';
            $iconHtml = "<span class=\"ds-btn__icon ds-btn__icon--{$iconPosition}\">{$attrs['icon']}</span>";
            unset($attrs['icon'], $attrs['iconPosition']);
        }
        
        // Construir atributos
        $attrStr = 'class="' . implode(' ', $classes) . '"';
        foreach ($attrs as $key => $value) {
            if ($value === true) {
                $attrStr .= " {$key}";
            } elseif ($value !== false && $value !== null) {
                $attrStr .= " {$key}=\"" . esc($value) . "\"";
            }
        }
        
        // Default type para botones
        if ($tag === 'button' && !isset($attrs['type'])) {
            $attrStr .= ' type="button"';
        }
        
        $content = $iconHtml ? ($iconHtml . $text) : $text;
        
        return "<{$tag} {$attrStr}>{$content}</{$tag}>";
    }
}

if (!function_exists('ds_card')) {
    /**
     * Genera una card del Design System
     * 
     * @param string $title Título de la card (opcional)
     * @param string $content Contenido HTML de la card
     * @param array $options Opciones: variant, footer, headerActions, class
     * @return string HTML de la card
     */
    function ds_card(string $content, ?string $title = null, array $options = []): string
    {
        $classes = ['ds-card'];
        
        if (isset($options['variant'])) {
            $classes[] = "ds-card--{$options['variant']}";
        }
        
        if (isset($options['class'])) {
            $classes[] = $options['class'];
        }
        
        $html = '<div class="' . implode(' ', $classes) . '">';
        
        // Header
        if ($title || isset($options['headerActions'])) {
            $html .= '<div class="ds-card__header">';
            if ($title) {
                $html .= '<h3 class="ds-card__title">' . esc($title) . '</h3>';
            }
            if (isset($options['headerActions'])) {
                $html .= '<div class="ds-card__actions">' . $options['headerActions'] . '</div>';
            }
            $html .= '</div>';
        }
        
        // Body
        $html .= '<div class="ds-card__body">' . $content . '</div>';
        
        // Footer
        if (isset($options['footer'])) {
            $html .= '<div class="ds-card__footer">' . $options['footer'] . '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('ds_alert')) {
    /**
     * Genera una alerta del Design System
     * 
     * @param string $message Mensaje de la alerta
     * @param string $type Tipo: info, success, warning, danger
     * @param array $options Opciones: title, dismissible, icon
     * @return string HTML de la alerta
     */
    function ds_alert(string $message, string $type = 'info', array $options = []): string
    {
        $icons = [
            'info' => 'ℹ️',
            'success' => '✅',
            'warning' => '⚠️',
            'danger' => '❌'
        ];
        
        $icon = $options['icon'] ?? ($icons[$type] ?? 'ℹ️');
        $dismissible = $options['dismissible'] ?? true;
        
        $html = '<div class="ds-alert ds-alert--' . $type . '">';
        $html .= '<span class="ds-alert__icon">' . $icon . '</span>';
        $html .= '<div class="ds-alert__content">';
        
        if (isset($options['title'])) {
            $html .= '<span class="ds-alert__title">' . esc($options['title']) . '</span>';
        }
        
        $html .= '<p class="ds-alert__text">' . $message . '</p>';
        $html .= '</div>';
        
        if ($dismissible) {
            $html .= '<button class="ds-alert__close" onclick="this.parentElement.remove()">×</button>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('ds_badge')) {
    /**
     * Genera un badge del Design System
     * 
     * @param string $text Texto del badge
     * @param string $variant Variante: primary, secondary, success, warning, danger, info, soft-*, outline-*
     * @param array $options Opciones: size, icon, pill
     * @return string HTML del badge
     */
    function ds_badge(string $text, string $variant = 'primary', array $options = []): string
    {
        $classes = ['ds-badge', "ds-badge--{$variant}"];
        
        if (isset($options['size'])) {
            $classes[] = "ds-badge--{$options['size']}";
        }
        
        if (isset($options['pill']) && $options['pill']) {
            $classes[] = 'ds-badge--pill';
        }
        
        $iconHtml = '';
        if (isset($options['icon'])) {
            $iconHtml = '<span class="ds-badge__icon">' . $options['icon'] . '</span>';
        }
        
        return '<span class="' . implode(' ', $classes) . '">' . $iconHtml . esc($text) . '</span>';
    }
}

if (!function_exists('ds_input')) {
    /**
     * Genera un input del Design System
     * 
     * @param string $name Nombre del input
     * @param array $options Opciones: type, value, label, placeholder, required, error, help, size, icon
     * @return string HTML del input con label
     */
    function ds_input(string $name, array $options = []): string
    {
        $type = $options['type'] ?? 'text';
        $value = $options['value'] ?? '';
        $label = $options['label'] ?? null;
        $placeholder = $options['placeholder'] ?? '';
        $required = $options['required'] ?? false;
        $error = $options['error'] ?? null;
        $help = $options['help'] ?? null;
        $id = $options['id'] ?? $name;
        
        $inputClasses = ['ds-input'];
        if (isset($options['size'])) {
            $inputClasses[] = "ds-input--{$options['size']}";
        }
        if ($error) {
            $inputClasses[] = 'ds-input--error';
        }
        
        $html = '<div class="ds-form-group">';
        
        // Label
        if ($label) {
            $labelClass = $required ? 'ds-label ds-label--required' : 'ds-label';
            $html .= '<label for="' . esc($id) . '" class="' . $labelClass . '">' . esc($label) . '</label>';
        }
        
        // Input wrapper (para iconos)
        if (isset($options['icon'])) {
            $html .= '<div class="ds-input-wrapper">';
            $html .= '<span class="ds-input-icon">' . $options['icon'] . '</span>';
        }
        
        // Input
        $attrs = [
            'type' => $type,
            'name' => $name,
            'id' => $id,
            'value' => $value,
            'placeholder' => $placeholder,
            'class' => implode(' ', $inputClasses)
        ];
        
        if ($required) {
            $attrs['required'] = 'required';
        }
        
        if (isset($options['disabled']) && $options['disabled']) {
            $attrs['disabled'] = 'disabled';
        }
        
        if (isset($options['readonly']) && $options['readonly']) {
            $attrs['readonly'] = 'readonly';
        }
        
        $attrStr = '';
        foreach ($attrs as $key => $val) {
            $attrStr .= " {$key}=\"" . esc($val) . "\"";
        }
        
        $html .= "<input{$attrStr}>";
        
        if (isset($options['icon'])) {
            $html .= '</div>';
        }
        
        // Error message
        if ($error) {
            $html .= '<span class="ds-form-error">' . esc($error) . '</span>';
        }
        
        // Help text
        if ($help && !$error) {
            $html .= '<span class="ds-form-text">' . esc($help) . '</span>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('ds_select')) {
    /**
     * Genera un select del Design System
     * 
     * @param string $name Nombre del select
     * @param array $options_list Array de opciones [value => label] o [[value, label]]
     * @param array $options Opciones del campo: selected, label, required, error, help
     * @return string HTML del select
     */
    function ds_select(string $name, array $options_list, array $options = []): string
    {
        $selected = $options['selected'] ?? '';
        $label = $options['label'] ?? null;
        $required = $options['required'] ?? false;
        $error = $options['error'] ?? null;
        $id = $options['id'] ?? $name;
        $placeholder = $options['placeholder'] ?? 'Seleccionar...';
        
        $selectClasses = ['ds-input'];
        if ($error) {
            $selectClasses[] = 'ds-input--error';
        }
        
        $html = '<div class="ds-form-group">';
        
        // Label
        if ($label) {
            $labelClass = $required ? 'ds-label ds-label--required' : 'ds-label';
            $html .= '<label for="' . esc($id) . '" class="' . $labelClass . '">' . esc($label) . '</label>';
        }
        
        // Select
        $attrs = "name=\"{$name}\" id=\"{$id}\" class=\"" . implode(' ', $selectClasses) . "\"";
        if ($required) {
            $attrs .= ' required';
        }
        
        $html .= "<select {$attrs}>";
        
        // Placeholder option
        if ($placeholder) {
            $html .= '<option value="">' . esc($placeholder) . '</option>';
        }
        
        // Options
        foreach ($options_list as $value => $optLabel) {
            $isSelected = ($value == $selected) ? ' selected' : '';
            $html .= '<option value="' . esc($value) . '"' . $isSelected . '>' . esc($optLabel) . '</option>';
        }
        
        $html .= '</select>';
        
        // Error message
        if ($error) {
            $html .= '<span class="ds-form-error">' . esc($error) . '</span>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('ds_modal')) {
    /**
     * Genera la estructura HTML de un modal
     * 
     * @param string $id ID del modal
     * @param string $title Título del modal
     * @param string $content Contenido HTML del body
     * @param array $options Opciones: footer, size, centered
     * @return string HTML del modal
     */
    function ds_modal(string $id, string $title, string $content, array $options = []): string
    {
        $size = isset($options['size']) ? "ds-modal--{$options['size']}" : '';
        $centered = isset($options['centered']) && $options['centered'] ? 'ds-modal--centered' : '';
        
        $html = '<div class="ds-modal-overlay" id="' . esc($id) . '" onclick="if(event.target===this)closeModal(\'' . esc($id) . '\')">';
        $html .= '<div class="ds-modal ' . $size . ' ' . $centered . '">';
        
        // Header
        $html .= '<div class="ds-modal__header">';
        $html .= '<h3 class="ds-modal__title">' . esc($title) . '</h3>';
        $html .= '<button class="ds-modal__close" onclick="closeModal(\'' . esc($id) . '\')">×</button>';
        $html .= '</div>';
        
        // Body
        $html .= '<div class="ds-modal__body">' . $content . '</div>';
        
        // Footer
        if (isset($options['footer'])) {
            $footerClass = 'ds-modal__footer';
            if (isset($options['footerAlign'])) {
                $footerClass .= " ds-modal__footer--{$options['footerAlign']}";
            }
            $html .= '<div class="' . $footerClass . '">' . $options['footer'] . '</div>';
        }
        
        $html .= '</div></div>';
        
        return $html;
    }
}

if (!function_exists('ds_confirm_modal')) {
    /**
     * Genera un modal de confirmación
     * 
     * @param string $id ID del modal
     * @param string $title Título
     * @param string $message Mensaje de confirmación
     * @param string $confirmAction Acción del botón confirmar (onclick o href)
     * @param string $type Tipo: danger, warning, success, info
     * @return string HTML del modal
     */
    function ds_confirm_modal(string $id, string $title, string $message, string $confirmAction, string $type = 'danger'): string
    {
        $icons = [
            'danger' => '⚠️',
            'warning' => '❓',
            'success' => '✅',
            'info' => 'ℹ️'
        ];
        
        $btnVariant = $type === 'danger' ? 'danger' : 'primary';
        
        $content = '<div class="ds-modal__icon ds-modal__icon--' . $type . '">' . ($icons[$type] ?? '❓') . '</div>';
        $content .= '<h4 class="ds-modal__title">' . esc($title) . '</h4>';
        $content .= '<p class="ds-modal__text">' . esc($message) . '</p>';
        
        $footer = ds_btn('Cancelar', 'light', ['onclick' => "closeModal('{$id}')"]);
        $footer .= ' ' . ds_btn('Confirmar', $btnVariant, ['onclick' => $confirmAction]);
        
        return ds_modal($id, '', $content, [
            'centered' => true,
            'size' => 'sm',
            'footer' => $footer,
            'footerAlign' => 'center'
        ]);
    }
}

if (!function_exists('ds_table_actions')) {
    /**
     * Genera botones de acción para tablas
     * 
     * @param array $actions Array de acciones: [['type' => 'edit', 'url' => '...'], ['type' => 'delete', 'onclick' => '...']]
     * @return string HTML de los botones
     */
    function ds_table_actions(array $actions): string
    {
        $html = '<div class="ds-table__actions">';
        
        $actionConfig = [
            'view' => ['icon' => '👁️', 'variant' => 'info', 'title' => 'Ver'],
            'edit' => ['icon' => '✏️', 'variant' => 'primary', 'title' => 'Editar'],
            'delete' => ['icon' => '🗑️', 'variant' => 'danger', 'title' => 'Eliminar'],
            'print' => ['icon' => '🖨️', 'variant' => 'secondary', 'title' => 'Imprimir'],
            'download' => ['icon' => '📥', 'variant' => 'success', 'title' => 'Descargar'],
        ];
        
        foreach ($actions as $action) {
            $type = $action['type'] ?? 'view';
            $config = $actionConfig[$type] ?? $actionConfig['view'];
            
            $attrs = [
                'size' => 'sm',
                'title' => $action['title'] ?? $config['title']
            ];
            
            if (isset($action['url'])) {
                $attrs['href'] = $action['url'];
            }
            if (isset($action['onclick'])) {
                $attrs['onclick'] = $action['onclick'];
            }
            if (isset($action['id'])) {
                $attrs['id'] = $action['id'];
            }
            
            $html .= ds_btn($config['icon'], "outline-{$config['variant']}", $attrs) . ' ';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('ds_empty_state')) {
    /**
     * Genera un estado vacío para tablas o listas
     * 
     * @param string $message Mensaje a mostrar
     * @param string $icon Icono (emoji o HTML)
     * @param string|null $actionBtn HTML del botón de acción (opcional)
     * @return string HTML del estado vacío
     */
    function ds_empty_state(string $message, string $icon = '📭', ?string $actionBtn = null): string
    {
        $html = '<div class="ds-empty-state">';
        $html .= '<div class="ds-empty-state__icon">' . $icon . '</div>';
        $html .= '<p class="ds-empty-state__text">' . esc($message) . '</p>';
        
        if ($actionBtn) {
            $html .= '<div class="ds-empty-state__action">' . $actionBtn . '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('ds_stat_card')) {
    /**
     * Genera una stat card para dashboards
     * 
     * @param string $value Valor a mostrar
     * @param string $label Etiqueta descriptiva
     * @param string $icon Icono
     * @param string $variant Variante de color: primary, success, warning, danger
     * @return string HTML de la stat card
     */
    function ds_stat_card(string $value, string $label, string $icon = '📊', string $variant = 'primary'): string
    {
        return '
        <div class="ds-card">
            <div class="ds-stat-card ds-stat-card--' . $variant . '">
                <div class="ds-stat-card__icon">' . $icon . '</div>
                <span class="ds-stat-card__value">' . esc($value) . '</span>
                <span class="ds-stat-card__label">' . esc($label) . '</span>
            </div>
        </div>';
    }
}

if (!function_exists('ds_breadcrumb')) {
    /**
     * Genera un breadcrumb
     * 
     * @param array $items Array de items: [['title' => '...', 'url' => '...'], ['title' => '...', 'active' => true]]
     * @return string HTML del breadcrumb
     */
    function ds_breadcrumb(array $items): string
    {
        $html = '<ul class="ds-breadcrumb">';
        $html .= '<li class="ds-breadcrumb__item"><a href="' . base_url('/') . '" class="ds-breadcrumb__link">🏠</a></li>';
        
        foreach ($items as $item) {
            $html .= '<li class="ds-breadcrumb__item">';
            
            if (!empty($item['active'])) {
                $html .= '<span class="ds-breadcrumb__current">' . esc($item['title']) . '</span>';
            } else {
                $html .= '<a href="' . esc($item['url'] ?? '#') . '" class="ds-breadcrumb__link">' . esc($item['title']) . '</a>';
            }
            
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        
        return $html;
    }
}
