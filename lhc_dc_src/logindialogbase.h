#ifndef LOGINDIALOGBASE_H
#define LOGINDIALOGBASE_H

#include <QDialog>
#include "ui_logindialogbase.h"

/**
* @brief There we can extend layout if we want
**/
class LoginDialogBase : public QDialog
{
	Q_OBJECT

public:
	LoginDialogBase(QWidget *parent = 0);
	~LoginDialogBase();

protected:
	Ui::LoginDialogBase ui;

private slots:
	virtual void on_cancelButton_clicked();
	virtual void on_okButton_clicked();
};

#endif // LOGINDIALOGBASE_H
