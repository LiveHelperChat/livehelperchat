#ifndef LHC_TRANSFERDIALOG_H
#define LHC_TRANSFERDIALOG_H

#include <QtGui>
#include <QDialog>
#include <QButtonGroup>

class LhcTransferDialog : public QDialog
{
    Q_OBJECT
public:
    LhcTransferDialog(int chatID, QWidget *parent = 0);

    ~LhcTransferDialog();

    int chatID;

QPushButton *okButton,
            *cancelButton;

QSpacerItem *spacerItem;

QGroupBox 
        /**
        * Action groups
        */
        *onlineUsersGroupBox;

QVBoxLayout         
        *onlineUsersBoxLayout,
        *mainLayout;

QButtonGroup
        *usersRadioGroup;

QHBoxLayout *hboxLayout;

    static void onlineUsersCallback(void* pt2Object, QByteArray result);

private slots:
    void on_cancelButton_clicked();
    void on_okButton_clicked();
};

#endif
