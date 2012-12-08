#include <QApplication>
#include <QDebug>

#include "webservice.h"
#include "logindialog.h"


#define DEBUG


LhcWebServiceClient *LhcWebServiceClient::instance() {
static LhcWebServiceClient* fac = 0;
if (fac == 0 ) {
    fac = new LhcWebServiceClient();
		
    if (qApp != 0) { 
        try {
           fac->setParent(qApp); 		  
        }
        catch (...) {
            qDebug() << QString("%1 %2")
                        .arg("LHCWebServiceInstance::instance()")
                        .arg("failed to setParent");
        }
    }
}
return fac; 
};


LhcWebServiceClient::LhcWebServiceClient()
{

	URL = new QString();
	DomainURL = new QString();
	URLPostAddress = new QString();
	QhttpClient = new QHttp();
	QHttpHeader = new QHttpRequestHeader();
	connect(QhttpClient, SIGNAL(requestFinished(int,bool)), this, SLOT(requestFinished(int,bool)));

	#ifdef DEBUG
		qDebug("URL fetch constructor - %s", URL->toStdString().c_str());
	#endif
}


/**
* Sets main fetch url and headers
*/
void LhcWebServiceClient::setFetchURL(QString urlFetch)
{
	*URL = urlFetch;
	QStringList lst( URL->split ("/") );
	QStringList::Iterator it2 = lst.begin();
	*DomainURL= *it2;
	*URLPostAddress = URL->replace(*DomainURL,"");
		
    QhttpClient->setHost(*DomainURL);

	QHttpHeader->setRequest("POST", *URLPostAddress);
	QHttpHeader->setValue("Host", *DomainURL);
	QHttpHeader->setValue("User-Agent", "Live helper chat XML client");
	QHttpHeader->setContentType("application/x-www-form-urlencoded");

	#ifdef DEBUG
		//qDebug("URL fetch assigned - %s", URL->toStdString().c_str());
	#endif

}

void LhcWebServiceClient::LhcSendRequestAuthorization(QStringList query,QString address,QObject* pt2Object, void (*pt2Function)(void* pt2Object, QByteArray))
{

	QString searchStrin = query.join("&");
    QHttpHeader->setRequest("POST", *URLPostAddress+"index.php"+address); 

    OperationQueStruc reqstruc;
    reqstruc.pt2Function = pt2Function;
    reqstruc.pt2Object = pt2Object;

	this->OperQuee.insert(QhttpClient->request(*QHttpHeader,searchStrin.toUtf8()), reqstruc);
}

/**
* Used for standard request with logins
*/
void LhcWebServiceClient::LhcSendRequest(QStringList query,QString address,QObject* pt2Object, void (*pt2Function)(void* pt2Object, QByteArray))
{

	QString searchStrin = query.join("&")+"&username="+username+"&password="+password;
    QHttpHeader->setRequest("POST", *URLPostAddress+"index.php"+address); 

    OperationQueStruc reqstruc;
    reqstruc.pt2Function = pt2Function;
    reqstruc.pt2Object = pt2Object;

	this->OperQuee.insert(QhttpClient->request(*QHttpHeader,searchStrin.toUtf8()), reqstruc);
}

/**
* Request without parameters additional,
* @TODO: 
* make GET from post.
*/
void LhcWebServiceClient::LhcSendRequest(QString address,QObject* pt2Object, void (*pt2Function)(void* pt2Object, QByteArray))
{

    QHttpHeader->setRequest("POST", *URLPostAddress+"index.php"+address); 

    OperationQueStruc reqstruc;
    reqstruc.pt2Function = pt2Function;
    reqstruc.pt2Object = pt2Object;

    QString auth = "username="+username+"&password="+password;

	this->OperQuee.insert(QhttpClient->request(*QHttpHeader,auth.toUtf8()), reqstruc);
}

void LhcWebServiceClient::LhcSendRequest(QStringList query,QString address)
{
    QString searchString = query.join("&");
    QHttpHeader->setRequest("POST", *URLPostAddress+"index.php"+address);
    searchString = searchString + "&username="+username+"&password="+password;    
    QhttpClient->request(*QHttpHeader,searchString.toUtf8());
}


void LhcWebServiceClient::LhcSendRequest(QString address)
{   
    QHttpHeader->setRequest("POST", *URLPostAddress+"index.php"+address);
    QString auth = "username="+username+"&password="+password;   
    QhttpClient->request(*QHttpHeader,auth.toUtf8());
    //qDebug("Debug %s",address.toStdString().c_str());
}

void LhcWebServiceClient::requestFinished(int requestID,bool error)
{

	#ifdef DEBUG
		if (error == true)
			qDebug("Could not connect - %s",QhttpClient->errorString().toStdString().c_str());
		else
			//qDebug("Succesfuly connected - %s",QhttpClient->errorString().toStdString().c_str());
			//qDebug("Request finished %d",requestID);
			//qDebug("Request finished value %d",this->OperQuee.value(requestID));			
			//qDebug("Queq size %d", error);		
			//this->OperQuee.value(requestID);
	#endif
		

	if (!this->OperQuee.isEmpty())
	{    
        if (this->OperQuee.contains(requestID) && error == false)
        {   
            QByteArray result = QhttpClient->readAll();            
            OperationQueStruc reqstruc = static_cast< OperationQueStruc > (this->OperQuee.take(requestID));

            // Associated with Quarded pointers, 
            // if some object destroyed before request finishes.
            if (reqstruc.pt2Object)
            reqstruc.pt2Function(reqstruc.pt2Object,result);

        } else {
            this->OperQuee.take(requestID);
        } 
	}
}



