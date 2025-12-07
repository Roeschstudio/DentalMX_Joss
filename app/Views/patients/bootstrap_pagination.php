<?php
$pager->setSurroundCount(2);
?>

<nav aria-label="Page navigation" class="ds-pagination">
    <ul class="ds-pagination">
        <?php if ($pager->hasPrevious()) : ?>
            <li class="ds-pagination__item">
                <a href="<?= $pager->getFirst() ?>" class="ds-pagination__link" aria-label="<?= lang('Pager.first') ?>">
                    ⏮️
                    <span><?= lang('Pager.first') ?></span>
                </a>
            </li>
            <li class="ds-pagination__item">
                <a href="<?= $pager->getPrevious() ?>" class="ds-pagination__link" aria-label="<?= lang('Pager.previous') ?>">
                    ◀️
                    <span><?= lang('Pager.previous') ?></span>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="ds-pagination__item <?= $link['active'] ? 'is-active' : '' ?>">
                <?php if ($link['active']) : ?>
                    <span class="ds-pagination__link">
                        <?= $link['title'] ?>
                    </span>
                <?php else : ?>
                    <a href="<?= $link['uri'] ?>" class="ds-pagination__link">
                        <?= $link['title'] ?>
                    </a>
                <?php endif ?>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <li class="ds-pagination__item">
                <a href="<?= $pager->getNext() ?>" class="ds-pagination__link" aria-label="<?= lang('Pager.next') ?>">
                    <span><?= lang('Pager.next') ?></span>
                    ▶️
                </a>
            </li>
            <li class="ds-pagination__item">
                <a href="<?= $pager->getLast() ?>" class="ds-pagination__link" aria-label="<?= lang('Pager.last') ?>">
                    <span><?= lang('Pager.last') ?></span>
                    ⏭️
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>
