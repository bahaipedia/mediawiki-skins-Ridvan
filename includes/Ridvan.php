<?php

class SkinRidvan extends SkinMustache {

    public function getTemplateData() {
        $data = parent::getTemplateData();

        // ---------------------------------------------------------
        // PART 1: HEADER BUTTONS
        // ---------------------------------------------------------
        $allPortlets = array_merge(
            $data['data-portlets']['data-namespaces']['array-items'] ?? [],
            $data['data-portlets']['data-views']['array-items'] ?? [],
            $data['data-portlets']['data-actions']['array-items'] ?? []
        );

        $editButton = null;
        $talkButton = null;
        $hybridMenu = [];

        foreach ( $allPortlets as $item ) {
            $id = $item['id'] ?? '';
            
            // CHECK 1: Edit Button
            if ( $id === 'ca-edit' || $id === 'ca-viewsource' ) {
                $editButton = $item;
            } 
            // CHECK 2: Talk Button
            elseif ( $id === 'ca-talk' || $id === 'ca-nstab-talk' ) {
                $talkButton = $item;
            } 
            // CHECK 3: Everything else -> Hybrid Menu
            else {
                $hybridMenu[] = $item;
            }
        }

        $data['ridvan-content-edit'] = $editButton ? [ $editButton ] : [];
        $data['ridvan-content-talk'] = $talkButton ? [ $talkButton ] : [];
        $data['ridvan-content-hybrid'] = $hybridMenu;


        // ---------------------------------------------------------
        // PART 2: HIDE LANGUAGES IF EMPTY OR ONLY "ADD LINKS"
        // ---------------------------------------------------------
        if ( isset( $data['data-portlets']['data-languages']['array-items'] ) ) {
            $langItems = $data['data-portlets']['data-languages']['array-items'];
            
            // Filter: Keep items that DO NOT have the 'wbc-editpage' class
            $realLanguages = array_filter( $langItems, function( $item ) {
                $class = $item['class'] ?? '';
                return strpos( $class, 'wbc-editpage' ) === false;
            });

            // If no real languages are left, delete the whole language block
            if ( empty( $realLanguages ) ) {
                unset( $data['data-portlets']['data-languages'] );
            }
        } else {
            // If the array doesn't exist at all, ensure the key is unset
            unset( $data['data-portlets']['data-languages'] );
        }

        return $data;
    }
}
