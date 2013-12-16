#include <QtNetwork/QHttp>
#include <QtNetwork/QHttpRequestHeader>
#include <QStringList>
#include <QPointer>
#include <QtNetwork/QNetworkAccessManager>
#include "objectfactory.h"

// Used quarded pointer to avoid dandling pointers.
// @see http://doc.trolltech.com/4.5/qpointer.html

struct OperationQueStruc {
    void (*pt2Function)(void* pt2Object, QByteArray);
    //void *pt2Object;
    QPointer<QObject> pt2Object;
};

typedef QMap<int, OperationQueStruc> mapOperation;


class LhcWebServiceClient : public ObjectFactory
{
Q_OBJECT
public:	
    static LhcWebServiceClient* instance();

	mapOperation OperQuee; // Que used to store http request que

    void LhcSendRequestAuthorization(QStringList query,QString address,QObject* pt2Object, void (*pt2Function)(void* pt2Object, QByteArray));

    // Request with parameters and with callback function
    void LhcSendRequest(QStringList query,QString address,QObject* pt2Object, void (*pt2Function)(void* pt2Object, QByteArray));

    // Request without parameters with callback function
    void LhcSendRequest(QString address,QObject* pt2Object, void (*pt2Function)(void* pt2Object, QByteArray));

    // Request with parameters without callback function
    void LhcSendRequest(QStringList query,QString address);

    // Request without parameters and without callback
    void LhcSendRequest(QString address);


    QString *URL,
			*DomainURL,
			*URLPostAddress;

    QString username, password;

    inline void setLogins(QString user,QString pass) {username=user;password=pass;};

protected:    
        LhcWebServiceClient();


public slots:
        void setFetchURL(QString urlFetch, QHttp::ConnectionMode mode = QHttp::ConnectionModeHttp);

private slots:
        void requestFinished(int requestID,bool error);
private:
    //QNetworkAccessManager *QhttpClient;
    //QNetworkRequest *QHttpHeader;
    //QNetworkReply *currentReply;

    QHttp *QhttpClient;
    QHttpRequestHeader *QHttpHeader;


    //QHttpRequestHeader *QHttpHeader;

};
