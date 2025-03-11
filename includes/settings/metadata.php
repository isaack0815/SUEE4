<?php
return [
    'title' => 'Metadaten',
    'template' => 'admin/settings/metadata.tpl',
    'load' => function() {
        global $db;
        return $db->select("SELECT * FROM metadata ORDER BY `meta_key`");
    },
    'save' => function($data) {
        global $db;
        
        if (isset($data['action'])) {
            switch ($data['action']) {
                case 'add':
                    if (!empty($data['meta_key']) && isset($data['meta_value'])) {
                        $db->insert('metadata', [
                            'key' => $data['meta_key'],
                            'value' => $data['meta_value'],
                            'description' => $data['description'] ?? ''
                        ]);
                    }
                    break;
                case 'edit':
                    if (!empty($data['id']) && !empty($data['meta_key']) && isset($data['meta_value'])) {
                        $db->update('metadata', 
                            [
                                'key' => $data['meta_key'],
                                'value' => $data['meta_value'],
                                'description' => $data['description'] ?? ''
                            ],
                            'id = ?',
                            [$data['id']]
                        );
                    }
                    break;
                case 'delete':
                    if (!empty($data['id'])) {
                        $db->delete('metadata', 'id = ?', [$data['id']]);
                    }
                    break;
            }
        }
    }
];

