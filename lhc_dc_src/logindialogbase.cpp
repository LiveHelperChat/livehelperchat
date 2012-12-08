#include "logindialogbase.h"

#include <QString>
#include <QtGui>

LoginDialogBase::LoginDialogBase(QWidget *parent)
	: QDialog(parent)
{
	ui.setupUi(this);
}

LoginDialogBase::~LoginDialogBase()
{

}


void LoginDialogBase::on_okButton_clicked()
{

}

void LoginDialogBase::on_cancelButton_clicked()
{
	
}

