#include "timeeditdelegate.h"

#include <QModelIndex>
#include <QPainter>
#include <QDateTime>

void TimeEditDelegate::paint(QPainter *painter, const QStyleOptionViewItem &option,
                         const QModelIndex &index) const
{
    int datetime = index.model()->data(index, Qt::DisplayRole).toInt();
  
    QString indexvalue = "";

    if (datetime > 0)
    {       
        QDateTime dateTime2 = QDateTime();
        dateTime2.setTime_t(datetime);
        indexvalue = dateTime2.toString(this->timeformat);
    }
    else
    {
        indexvalue = tr("Date not set");
    }

    Q_ASSERT(index.isValid());

    QStyleOptionViewItemV3 opt = setOptions(index, option);

    const QStyleOptionViewItemV2 *v2 = qstyleoption_cast<const QStyleOptionViewItemV2 *>(&option);
    opt.features = v2 ? v2->features
                    : QStyleOptionViewItemV2::ViewItemFeatures(QStyleOptionViewItemV2::None);
    const QStyleOptionViewItemV3 *v3 = qstyleoption_cast<const QStyleOptionViewItemV3 *>(&option);
    opt.locale = v3 ? v3->locale : QLocale();
    opt.widget = v3 ? v3->widget : 0;

    // prepare
    painter->save();
   
    painter->setClipRect(opt.rect);

    // get the data and the rectangles
    QVariant value;

    QPixmap pixmap;
    QRect decorationRect;
    value = index.data(Qt::DecorationRole);
 
    QString text;
    QRect displayRect;
    value = index.data(Qt::DisplayRole);
    if (value.isValid()) {
        text = indexvalue;
        displayRect = textRectangle(painter, option.rect, opt.font, text);
    }

    QRect checkRect;
    Qt::CheckState checkState = Qt::Unchecked;
    value = index.data(Qt::CheckStateRole);
    if (value.isValid()) {
        checkState = static_cast<Qt::CheckState>(value.toInt());
        checkRect = check(opt, opt.rect, value);
    }

    // do the layout
    doLayout(opt, &checkRect, &decorationRect, &displayRect, false);
    // draw the item

    drawBackground(painter, opt, index);
    drawCheck(painter, opt, checkRect, checkState);
    drawDecoration(painter, opt, decorationRect, pixmap);
    drawDisplay(painter, opt, displayRect, text);
    drawFocus(painter, opt, displayRect);

    // done
    painter->restore();
}
