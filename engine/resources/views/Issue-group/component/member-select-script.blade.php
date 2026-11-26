{{-- add ar edit duitate: position auto serial + "All" diye role wise eksathe check/uncheck. $container = jQuery selector --}}
<script>
    $(document).ready(function () {
        const container = @json($container);
        const posOf = cb => $(cb).closest('.align-items-center').find('.position-input');

        // sob tick thakle "All" o tick, na hole untick
        function syncSelectAll($card, role) {
            const $boxes = $card.find('.' + role + '-checkbox');
            $card.find('.select-all-role[data-role="' + role + '"]')
                .prop('checked', $boxes.length > 0 && $boxes.length === $boxes.filter(':checked').length);
        }

        // checkbox tick korle oi subgroup er oi role (Maker/Checker) e porer position auto bose, untick korle porer gula ek ghor kome
        $(container).on('change', '.maker-checkbox, .checker-checkbox', function () {
            const role = $(this).hasClass('maker-checkbox') ? 'maker' : 'checker';
            const $card = $(this).closest('.card');
            const $pos = posOf(this);
            const rolePositions = () => $card.find('.' + role + '-checkbox:checked').not(this)
                .closest('.align-items-center').find('.position-input');

            if (this.checked) {
                let max = 0;
                rolePositions().each(function () {
                    max = Math.max(max, parseInt($(this).val()) || 0);
                });
                $pos.val(max + 1);
            } else {
                const removed = parseInt($pos.val()) || 0;
                $pos.val('');
                if (removed) {
                    rolePositions().each(function () {
                        const val = parseInt($(this).val()) || 0;
                        if (val > removed) $(this).val(val - 1);
                    });
                }
            }

            syncSelectAll($card, role);
        });

        // "All" diye oi subgroup er oi role er sob user eksathe check/uncheck; age tick kora gular position thake, notun gula porer serial pay
        $(container).on('change', '.select-all-role', function () {
            const $boxes = $(this).closest('.card').find('.' + $(this).data('role') + '-checkbox');

            if (this.checked) {
                let max = 0;
                $boxes.filter(':checked').each(function () {
                    max = Math.max(max, parseInt(posOf(this).val()) || 0);
                });
                $boxes.not(':checked').each(function () {
                    $(this).prop('checked', true);
                    posOf(this).val(++max);
                });
            } else {
                $boxes.each(function () {
                    $(this).prop('checked', false);
                    posOf(this).val('');
                });
            }
        });

        // section button er data-role: all = Maker + Checker, maker = sudhu Maker, checker = sudhu Checker
        const sectionRoles = role => role === 'all' ? ['maker', 'checker'] : [role];
        const sectionLabels = {all: 'All', maker: 'All Maker', checker: 'All Checker'};

        // section (Next Level / Touch) er oi role er sob user tick thakle button "Unselect ...", na hole "Select ..."
        function syncSectionBtn($section) {
            $section.find('.select-all-section').each(function () {
                const role = $(this).data('role') || 'all';
                const $boxes = $section.find(sectionRoles(role).map(r => '.' + r + '-checkbox').join(', '));
                const allChecked = $boxes.length > 0 && $boxes.length === $boxes.filter(':checked').length;
                $(this).text((allChecked ? 'Unselect ' : 'Select ') + sectionLabels[role]);
            });
        }

        // heading er button diye oi section er sob subgroup er role wise eksathe check/uncheck (card er "All" er logic reuse)
        $(document).on('click', '.select-all-section', function () {
            const $section = $(this).closest('.group-section');
            const roles = sectionRoles($(this).data('role') || 'all');
            const $boxes = $section.find(roles.map(r => '.' + r + '-checkbox').join(', '));
            const check = $boxes.length !== $boxes.filter(':checked').length;

            $section.find(roles.map(r => '.select-all-role[data-role="' + r + '"]').join(', ')).each(function () {
                $(this).prop('checked', check).trigger('change');
            });
            syncSectionBtn($section);
        });

        $(document).on('change', '.group-section .maker-checkbox, .group-section .checker-checkbox', function () {
            syncSectionBtn($(this).closest('.group-section'));
        });

        // edit e age theke tick kora data onujayi "All" er state
        $(container).find('.card').each(function () {
            syncSelectAll($(this), 'maker');
            syncSelectAll($(this), 'checker');
        });
        $('.group-section').each(function () {
            syncSectionBtn($(this));
        });
    });
</script>
